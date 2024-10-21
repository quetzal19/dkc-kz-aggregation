<?php

namespace App\Tests\Api\v1\Accessory;

use App\Tests\{Helper\Api\PaginationHelper, Helper\Api\ProductHelper, Helper\Api\ValidationHelper, Support\ApiTester};
use Codeception\Util\HttpCode;

class GetAccessoryProductCest
{
    /**
     * @throws \Exception
     */
    public function successStructure(ApiTester $I): void
    {
        $page = 1;
        $limit = 10;

        $I->sendGet("/accessories/products/?productCode=". ProductHelper::PRODUCT_CODE . "&limit=$limit&page=$page");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                'products' => [],
            ],
            'errors' => []
        ]);

        $data = $I->grabDataFromResponseByJsonPath('data[*]')[0];

        if (!empty($data)) {
            foreach (ProductHelper::RESPONSE_ACCESSORY_PRODUCT_FIELDS as $field) {
                $I->seeResponseJsonMatchesJsonPath("data[*][0][$field]");
            }
        }
    }

    public function withoutProductCode(ApiTester $I): void
    {
        $I->sendGet('/accessories/products/');

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

    public function invalidParams(ApiTester $I): void
    {
        $page = "page";
        $limit = 9999;

        $I->sendGet("/accessories/products/?limit=$limit&page=$page");
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson([
            'status' => HttpCode::BAD_REQUEST,
            'message' => ValidationHelper::VALIDATION_ERROR_MESSAGE,
            'detail' => null,
            'validationError' => [
                'body' => [
                    ProductHelper::MISSING_PRODUCT_CODE_VALIDATION_ERROR,
                    PaginationHelper::INVALID_PAGE_VALIDATION_ERROR,
                    PaginationHelper::invalidLimitParam($limit)
                ]
            ]
        ]);
    }
}