<?php

namespace backend\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Class Menu
 * Theme menu widget.
 */
class Menu extends \dmstr\widgets\Menu
{
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            $arrayRoute = explode('/', ltrim($route, '/'));
            $arrayThisRoute = explode('/', $this->route);
            $routeCount = count($arrayRoute);


            if ($routeCount == 2) {
                if ($arrayRoute[0] !== $arrayThisRoute[0]) {
                    return false;
                }
            } elseif ($routeCount == 3) {
                if ($arrayRoute[0] !== $arrayThisRoute[0]) {
                    return false;
                }
                if (isset($arrayRoute[1]) && $arrayRoute[1] !== $arrayThisRoute[1]) {
                    return false;
                }
            } else {
                return false;
            }

            return true;
        }
        return false;

    }

    protected function isItemActive2($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {

            $thisRoute = trim(Yii::$app->request->getUrl(), '/');
            if (($index = strpos($thisRoute, '?')) != false) {
                $thisRoute = substr($thisRoute, 0, $index);
            }
            $arrayThisRoute = explode('/', $thisRoute);

            $route = trim($item['url'][0], '/');
            $arrayRoute = explode('/', $route);
            //改写了路由的规则，是否高亮判断到controller而非action

            $routeCount = count($arrayRoute);


            if ($routeCount == 2) {
                if ($arrayRoute[0] !== $arrayThisRoute[0]) {
                    return false;
                }
            } elseif ($routeCount == 3) {
                if ($arrayRoute[0] !== $arrayThisRoute[0]) {
                    return false;
                }
                if (isset($arrayRoute[1]) && $arrayRoute[1] !== $arrayThisRoute[1]) {
                    return false;
                }
            } else {
                return false;
            }

            return true;
        }
        return false;
    }
}
