<?php

namespace App\Tests\Api\v1\Analog;

use App\Tests\Helper\{ProductHelper, SectionHelper, ValidationHelper};
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

class GetAnalogSectionCest
{
    /**
     * @throws \Exception
     */
    public function successStructure(ApiTester $I): void
    {
        $I->sendGet('/analogs/sections/?productCode=' . ProductHelper::PRODUCT_CODE);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                'sections' => []
            ],
            'errors' => []
        ]);

        $data = $I->grabDataFromResponseByJsonPath('data[*]')[0];

        if (!empty($data)) {
            foreach (SectionHelper::RESPONSE_ACCESSORY_SECTIONS_FIELDS as $field) {
                $I->seeResponseJsonMatchesJsonPath("data[*][0][$field]");
            }
        }
    }

    public function withoutProductCode(ApiTester $I): void
    {
        $I->sendGet('/analogs/sections/');

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson([
            'status' => HttpCode::BAD_REQUEST,
            'message' => ValidationHelper::VALIDATION_ERROR_MESSAGE,
            'detail' => null,
            'validationError' => [
                'body' => [
                    ProductHelper::MISSING_PRODUCT_CODE_VALIDATION_ERROR
                ]
            ]
        ]);
    }
}