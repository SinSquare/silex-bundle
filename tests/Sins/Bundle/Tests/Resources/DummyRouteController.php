<?php

namespace SinSquare\Bundle\Tests\Resources;

use SinSquare\Bundle\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DummyRouteController extends AbstractController
{
    public function firstAction(Request $request)
    {
        return self::_success(__FUNCTION__, $request);
    }

    public function secondAction(Request $request)
    {
    }

    public function thirdAction(Request $request)
    {
    }

    public function fourthAction(Request $request)
    {
    }

    public static function _success($action, $request)
    {
        $data = array(
            'data' => array(
                'action' => $action,
                'request' => $request->request->all(),
                'query' => $request->query->all(),
            ),
        );

        return static::success($data);
    }
}
