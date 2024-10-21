<?php

namespace App\Tests\Api\v1\Accessory;

use App\Tests\Helper\Api\ProductHelper;
use App\Tests\Helper\Api\SectionHelper;
use App\Tests\Helper\Api\ValidationHelper;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

class GetAccessorySectionsCest
{
    /**
     * @throws \Exception
     */
    public function successStructure(ApiTester $I): void
    {
        $I->sendGet('/accessories/sections/?productCode=' . ProductHelper::PRODUCT_CODE);
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
        $I->sendGet('/accessories/sections/');

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