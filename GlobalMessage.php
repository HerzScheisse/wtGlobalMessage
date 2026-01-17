<?php

declare(strict_types=1);

namespace GlobalMessageNameSpace;

use Fisharebest\Localization\Translation;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleFooterTrait;
use Fisharebest\Webtrees\View;
use Jefferson49\Webtrees\Exceptions\GithubCommunicationError;
use Jefferson49\Webtrees\Helpers\GithubService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

class GlobalMessage extends AbstractModule implements ModuleCustomInterface, ModuleFooterInterface {
    use ModuleCustomTrait;
    use ModuleFooterTrait;

    public const CUSTOM_AUTHOR = 'HerzScheisse';
    public const CUSTOM_VERSION = '1.0.2';
    public const GITHUB_REPO = 'HerzScheisse/wtGlobalMessage';
    public const AUTHOR_WEBSITE = 'https://eisold.family';
    public const CUSTOM_SUPPORT_URL = 'https://github.com/' . self::GITHUB_REPO;
    public const CUSTOM_LATEST_VERSION = 'https://raw.githubusercontent.com/' . self::GITHUB_REPO . '/main/latest-version.txt';

    //Github API URL to get the information about the latest releases
    public const GITHUB_API_LATEST_VERSION = 'https://api.github.com/repos/'. self::GITHUB_REPO . '/releases/latest';
    public const GITHUB_API_TAG_NAME_PREFIX = '"tag_name":"v';

    public function title(): string
    {
        return I18N::translate('Global Message');
    }

    public function description(): string
    {
        return I18N::translate('Global Message description');
    }

    public function customModuleAuthorName(): string
    {
        return self::CUSTOM_AUTHOR;

    }

    public function customModuleVersion(): string
    {
        return self::CUSTOM_VERSION;
    }

    public function customModuleLatestVersion(): string
    {
        return Registry::cache()->file()->remember(
            $this->name() . '-latest-version',
            function (): string {

                try {
                    //Get latest release from GitHub
                    return GithubService::getLatestReleaseTag(self::GITHUB_REPO);
                }
                catch (GithubCommunicationError $ex) {
                    // Can't connect to GitHub?
                    return $this->customModuleVersion();
                }
            },
            86400
        );
    }

    public function customModuleSupportUrl(): string
    {
        return self::CUSTOM_SUPPORT_URL;
    }

    public function customTranslations(string $language): array
    {
        switch ($language) {
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return [
                    'Global Message'                => 'Global Message',
                    'Global Message description'    => 'This module adds a notice (flash message) at each page to remind the guest-user that the displayed data is limited (if you have added some restrictions in admin panel) and that they need to log in to see more data.',
                    'Global Message Text'           => 'You are not logged in! Displayed data is limited!',
                ];

            case 'de':
                return [
                    'Global Message'                => 'Globale Nachricht',
                    'Global Message description'    => 'Dieses Modul fügt auf jeder Seite einen Hinweis (Flash-Nachricht) hinzu, um den Gastbenutzer daran zu erinnern, dass die angezeigten Daten begrenzt sind (wenn Sie im Admin-Bereich einige Einschränkungen hinzugefügt haben) und dass er sich anmelden muss, um weitere Daten anzuzeigen.',
                    'Global Message Text'           => 'Du bist nicht eingeloggt! Angezeigte Daten sind stark limitiert!',
                ];

            case 'nl':
                return [
                    'Global Message'                => 'Globaal bericht',
                    'Global Message description'    => 'Deze module voegt op elke pagina een melding (flashbericht) toe om de gastgebruiker eraan te herinneren dat de weergegeven gegevens beperkt zijn (als u beperkingen hebt ingesteld in het controlepaneel) en dat ze moeten inloggen om meer gegevens te kunnen zien.',
                    'Global Message Text'           => 'U bent niet ingelogd! De weergegeven gegevens zijn beperkt!',
                ];

            default:
                return [];
        }
    }

    public function boot(): void
    {
        View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
    }

    public function resourcesFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    public function getFooter(ServerRequestInterface $request): string
    {
            $tree = $request->getAttribute('tree');

            $url = route('module', [
                'module' => $this->name(),
                'action' => 'Page',
                'tree'   => $tree ? $tree->name() : null,
            ]);

        if (Auth::check() === false) {
            return view($this->name() . '::message', ['url' => $url]);
        } else {
            return '';
        }
    }

};
