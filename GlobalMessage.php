<?php

/**
 * Global Message Text for Guests.
 */

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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

class GlobalMessage extends AbstractModule implements ModuleCustomInterface, ModuleFooterInterface {
    use ModuleCustomTrait;
    use ModuleFooterTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Global Message');
    }

    public function customModuleAuthorName(): string
    {
        return 'Holger André Eisold';

    }

    public function customModuleVersion(): string
    {
        return '1.0.1';
    }

    public function customModuleLatestVersionUrl(): string
    {
        return 'https://github.com/HerzScheisse/wtGlobalMessage/raw/main/latest-version.txt';
    }

    public function customModuleSupportUrl(): string
    {
        return 'https://github.com/HerzScheisse/wtGlobalMessage';
    }

    public function customTranslations(string $language): array
    {
        switch ($language) {
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return [
                    'Global Message'        => 'Global Message',
                    'Global Message Text'   => 'You are not logged in! Displayed data is limited!',
                ];

            case 'de':
                return [
                    'Global Message'        => 'Globale Nachricht',
                    'Global Message Text'   => 'Du bist nicht eingeloggt! Angezeigte Daten sind stark limitiert!',
                ];

            default:
                return [];
        }
    }

    public function boot(): void
    {
        // Register a namespace for our views.
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