<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\SEO\Widgets;

use Piwik\Common;
use Piwik\DataTable\Renderer;
use Piwik\Plugin\WidgetConfig;
use Piwik\Site;
use Piwik\Url;
use Piwik\UrlHelper;
use Piwik\View;
use Piwik\Plugins\SEO\API;

class GetRank extends \Piwik\Plugin\Widget
{
    public static function configure(WidgetConfig $config)
    {
        $config->setCategory('SEO');
        $config->setName('SEO_SeoRankings');
    }

    public function render()
    {
        $idSite = Common::getRequestVar('idSite');
        $site = new Site($idSite);

        $url = urldecode(Common::getRequestVar('url', '', 'string'));

        if (!empty($url) && strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = 'http://' . $url;
        }

        if (empty($url) || !UrlHelper::isLookLikeUrl($url)) {
            $url = $site->getMainUrl();
        }

        $dataTable = API::getInstance()->getRank($url);

        $view = new View('@SEO/getRank');
        $view->urlToRank = Url::getHostFromUrl($url);

        /** @var \Piwik\DataTable\Renderer\Php $renderer */
        $renderer = Renderer::factory('php');
        $renderer->setSerialize(false);
        $view->ranks = $renderer->render($dataTable);

        return $view->render();
    }

}
