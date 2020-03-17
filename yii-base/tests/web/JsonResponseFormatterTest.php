<?php
namespace ego\tests\base;

use yii;
use ego\enums\CommonError;
use ego\tests\TestCase;

class JsonResponseFormatterTest extends TestCase
{
    public function testFormat()
    {
        $response = app()->getResponse();
        /** @var \ego\web\JsonResponseFormatter $formatter */
        $formatter = Yii::createObject($response->formatters['json']);
        $this->assertSame($response->data, $formatter->format($response));
    }

    public function testParseResponse()
    {
        $response = app()->getResponse();
        /** @var \ego\web\JsonResponseFormatter $formatter */
        $formatter = Yii::createObject($response->formatters['json']);

        // json format -> false
        $this->assertSame(
            $response->data,
            $this->invokeMethod($formatter, 'parseResponse', [$response])
        );

        $response->format = 'json';
        $response->data = [
            'code' => 0,
            'message' => 'ok',
        ];
        $this->assertSame(
            $response->data,
            $this->invokeMethod($formatter, 'parseResponse', [$response])
        );

        $response->data = [
            'code' => -1,
            'message' => 'ok',
            'name' => 'ok',
            'status' => 500,
        ];
        $data = $this->invokeMethod($formatter, 'parseResponse', [$response]);
        $this->assertArrayNotHasKey('name', $data);
        $this->assertArrayNotHasKey('status', $data);

        $_GET[$formatter->jsonpCalbackName] = '?';
        $this->assertSame(
            '?',
            $this->invokeMethod($formatter, 'parseResponse', [$response])['callback']
        );

        unset($_GET[$formatter->jsonpCalbackName]);
        $response->data = [
            'code' => 0,
            'message' => 'ok',
        ];
        $response->statusCode = 400;
        $this->assertSame(
            CommonError::ERR_SYSTEM_BUSY,
            $this->invokeMethod($formatter, 'parseResponse', [$response])['code']
        );
    }
}
