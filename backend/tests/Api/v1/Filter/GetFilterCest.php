<?php

namespace App\Tests\Api\v1\Filter;

use App\Tests\{Helper\Api\FilterHelper, Helper\Api\SectionHelper, Helper\Api\ValidationHelper, Support\ApiTester};
use Codeception\Util\HttpCode;

class GetFilterCest
{
    public function successStructure(ApiTester $I): void
    {
        $I->sendGet('/filters/?sectionCode=' . SectionHelper::SECTION_CODE);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'filters' => [],
        ]);

        $data = $I->grabDataFromResponseByJsonPath('filters[*]');

        if (!empty($data)) {
            foreach (FilterHelper::RESPONSE_FILTER_FIELDS as $field) {
                $I->seeResponseJsonMatchesJsonPath("filters[*][$field]");
            }
        }
    }

    public function withoutSectionCode(ApiTester $I): void
    {
        $I->sendGet('/filters/');

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

        $I->seeResponseContainsJson([
            'status' => HttpCode::BAD_REQUEST,
            'message' => ValidationHelper::VALIDATION_ERROR_MESSAGE,
            'detail' => null,
            'validationError' => [
                'body' => [
                    SectionHelper::MISSING_SECTION_CODE_VALIDATION_ERROR
                ]
            ]
        ]);
    }

    public function invalidParams(ApiTester $I): void
    {
        $filters = 'filters';

        $I->sendGet('/filters/?filters=' . $filters);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

        $I->seeResponseContainsJson([
            'status' => HttpCode::BAD_REQUEST,
            'message' => ValidationHelper::VALIDATION_ERROR_MESSAGE,
            'detail' => null,
            'validationError' => [
                'body' => [
                    SectionHelper::MISSING_SECTION_CODE_VALIDATION_ERROR,
                    FilterHelper::FILTERS_INCORRECT_FORMAT_VALIDATION_ERROR
                ]
            ]
        ]);
    }
}