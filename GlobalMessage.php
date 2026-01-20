<?php

declare(strict_types=1);

namespace HerzScheisse\Webtrees\Module\GlobalMessage;

use Fisharebest\Localization\Translation;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\View;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleFooterTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GlobalMessage extends AbstractModule implements ModuleCustomInterface, ModuleFooterInterface
{
    use ModuleCustomTrait;
    use ModuleFooterTrait;

    public const CUSTOM_AUTHOR = 'Holger André Eisold';
    public const CUSTOM_VERSION = '1.0.3';
    public const GITHUB_REPO = 'HerzScheisse/wtGlobalMessage';
    public const AUTHOR_WEBSITE = 'https://eisold.family';
    public const CUSTOM_SUPPORT_URL = 'https://github.com/' . self::GITHUB_REPO;
    public const CUSTOM_LATEST_VERSION = 'https://raw.githubusercontent.com/' . self::GITHUB_REPO . '/main/latest-version.txt';

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

    public function customModuleLatestVersionUrl(): string
    {
        return self::CUSTOM_LATEST_VERSION;
    }

    public function customModuleSupportUrl(): string
    {
        return self::CUSTOM_SUPPORT_URL;
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

    public function customTranslations(string $language): array
    {
        $lang_dir = $this->resourcesFolder() . 'lang/';
        $file = $lang_dir . $language . '.php';

        return file_exists($file) ? (new Translation($file))->asArray() : [];
    }
}
