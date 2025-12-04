<?php

namespace Jensvandewiel\LaravelNotionApi\Query\Filters;

use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Query\QueryHelper;
use Illuminate\Support\Collection;

/**
 * Class Filter.
 */
class Filter extends QueryHelper
{
    /**
     * @var string|null
     */
    private ?string $filterType;
    /**
     * @var array|null
     */
    private ?array $filterConditions;
    /**
     * @var array|null
     */
    private ?array $filterDefinition;

    /**
     * Filter constructor.
     *
     * @param  string  $property
     * @param  string|null  $filterType
     * @param  array|null  $filterConditions
     * @param  array|null  $filterDefinition
     */
    public function __construct(
        string $property,
        string $filterType = null,
        array $filterConditions = null,
        array $filterDefinition = null
    ) {
        parent::__construct();

        $this->property = $property;
        $this->filterType = $filterType;
        $this->filterConditions = $filterConditions;
        $this->filterDefinition = $filterDefinition;
    }

    /**
     * Creates a text/rich_text filter instance after checking validity.
     *
     * @see https://developers.notion.com/reference/post-database-query#filter-condition-object
     *
     * @param  string  $property
     * @param  string  $comparisonOperator
     * @param  string  $value
     * @return Filter
     */
    public static function textFilter(string $property, string $comparisonOperator, string $value): Filter
    {
        // For Notion API 2025-09-03, text properties use 'rich_text' filter type
        self::isValidComparisonOperatorFor('rich_text', $comparisonOperator);

        return new Filter($property, 'rich_text', [$comparisonOperator => $value]);
    }

    /**
     * Creates a number filter instance after checking validity.
     *
     * @see https://developers.notion.com/reference/post-database-query#number-filter-condition
     *
     * @param  string  $property
     * @param  string  $comparisonOperator
     * @param  float|int  $number
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function numberFilter(string $property, string $comparisonOperator, $number): Filter
    {
        if (! is_numeric($number)) {
            throw new HandlingException('The number must be numeric.');
        }

        self::isValidComparisonOperatorFor('number', $comparisonOperator);

        return new Filter($property, 'number', [$comparisonOperator => $number]);
    }

    /**
     * Creates a checkbox filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#checkbox
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (equals, does_not_equal)
     * @param  bool  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function checkboxFilter(string $property, string $comparisonOperator, bool $value): Filter
    {
        self::isValidComparisonOperatorFor('checkbox', $comparisonOperator);

        return new Filter($property, 'checkbox', [$comparisonOperator => $value]);
    }

    /**
     * Creates a date filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#date
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (after, before, equals, on_or_before, on_or_after, next_week, next_month, next_year, past_week, past_month, past_year, this_week, is_empty, is_not_empty)
     * @param  string|null  $value (ISO 8601 date, null for empty checks and relative dates)
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function dateFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('date', $comparisonOperator);

        $conditions = [];

        // For relative dates (next_week, past_month, etc.), use empty object
        if (in_array($comparisonOperator, ['next_week', 'next_month', 'next_year', 'past_week', 'past_month', 'past_year', 'this_week'])) {
            $conditions[$comparisonOperator] = new \stdClass();
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'date', $conditions);
    }

    /**
     * Creates a files filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#files
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (is_empty, is_not_empty)
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function filesFilter(string $property, string $comparisonOperator): Filter
    {
        self::isValidComparisonOperatorFor('files', $comparisonOperator);

        return new Filter($property, 'files', [$comparisonOperator => true]);
    }

    /**
     * Creates a multi_select filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#multi_select
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (contains, does_not_contain, is_empty, is_not_empty)
     * @param  string|null  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function multiSelectFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('multi_select', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'multi_select', $conditions);
    }

    /**
     * Creates a select filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#select
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (equals, does_not_equal, is_empty, is_not_empty)
     * @param  string|null  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function selectFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('select', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'select', $conditions);
    }

    /**
     * Creates a status filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#status
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (equals, does_not_equal, is_empty, is_not_empty)
     * @param  string|null  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function statusFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('status', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'status', $conditions);
    }

    /**
     * Creates a people filter instance (for people, created_by, last_edited_by properties).
     *
     * @see https://developers.notion.com/reference/post-database-query#people
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (contains, does_not_contain, is_empty, is_not_empty)
     * @param  string|null  $value (UUIDv4)
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function peopleFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('people', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'people', $conditions);
    }

    /**
     * Creates a relation filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#relation
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (contains, does_not_contain, is_empty, is_not_empty)
     * @param  string|null  $value (UUIDv4)
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function relationFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('relation', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'relation', $conditions);
    }

    /**
     * Creates a phone_number filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#phone_number
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (contains, does_not_contain, equals, does_not_equal, starts_with, ends_with, is_empty, is_not_empty)
     * @param  string|null  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function phoneNumberFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('phone_number', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'phone_number', $conditions);
    }

    /**
     * Creates an email filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#email
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (contains, does_not_contain, equals, does_not_equal, starts_with, ends_with, is_empty, is_not_empty)
     * @param  string|null  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function emailFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('email', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'email', $conditions);
    }

    /**
     * Creates a URL filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#url
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (contains, does_not_contain, equals, does_not_equal, starts_with, ends_with, is_empty, is_not_empty)
     * @param  string|null  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function urlFilter(string $property, string $comparisonOperator, ?string $value = null): Filter
    {
        self::isValidComparisonOperatorFor('url', $comparisonOperator);

        $conditions = [];
        if (in_array($comparisonOperator, ['is_empty', 'is_not_empty'])) {
            $conditions[$comparisonOperator] = true;
        } else {
            $conditions[$comparisonOperator] = $value;
        }

        return new Filter($property, 'url', $conditions);
    }

    /**
     * Creates a unique_id (ID) filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#unique_id
     *
     * @param  string  $property
     * @param  string  $comparisonOperator (equals, does_not_equal, greater_than, less_than, greater_than_or_equal_to, less_than_or_equal_to)
     * @param  int  $value
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function uniqueIdFilter(string $property, string $comparisonOperator, int $value): Filter
    {
        self::isValidComparisonOperatorFor('unique_id', $comparisonOperator);

        return new Filter($property, 'unique_id', [$comparisonOperator => $value]);
    }

    /**
     * Creates a verification filter instance.
     *
     * @see https://developers.notion.com/reference/post-database-query#verification
     *
     * @param  string  $property
     * @param  string  $status (verified, expired, none)
     * @return Filter
     *
     * @throws HandlingException
     */
    public static function verificationFilter(string $property, string $status): Filter
    {
        if (!in_array($status, ['verified', 'expired', 'none'])) {
            throw new HandlingException('Invalid verification status. Must be: verified, expired, or none.');
        }

        return new Filter($property, 'verification', ['status' => $status]);
    }

    /**
     * This method allows you to define every filter that is offered
     * by Notion but not implemented in this package yet. Provide the
     * filter definition as an array like explained in the Notion docs.
     * Use with caution; this method will be removed in the future and
     * is marked as deprecated from the start!
     *
     * @see https://developers.notion.com/reference/post-database-query#post-database-query-filter
     *
     * @param  string  $property
     * @param  array  $filterDefinition
     * @return Filter
     *
     * @deprecated
     */
    public static function rawFilter(string $property, array $filterDefinition): Filter
    {
        return new Filter($property, null, null, $filterDefinition);
    }

    /**
     * @return array
     *
     * @throws HandlingException
     */
    public function toArray(): array
    {
        if ($this->filterDefinition !== null && $this->filterType === null && $this->filterConditions === null) {
            return array_merge(
                ['property' => $this->property],
                $this->filterDefinition
            );
        } elseif ($this->filterType !== null && $this->filterConditions !== null && $this->filterDefinition === null) {
            return [
                'property' => $this->property,
                $this->filterType => $this->filterConditions,
            ];
        } else {
            throw HandlingException::instance('Invalid filter definition.', ['invalidFilter' => $this]);
        }
    }

    /**
     * Semantic wrapper for toArray().
     *
     * @return array
     *
     * @throws HandlingException
     */
    public function toQuery(): array
    {
        return $this->toArray();
    }

    /**
     * @param  Collection  $filter
     * @return array
     *
     * @throws HandlingException
     */
    public static function filterQuery(Collection $filter): array
    {
        $queryFilter = new Collection();

        $filter->each(function ($filter) use ($queryFilter) {
            $queryFilter->add($filter->toQuery());
        });

        return $queryFilter->toArray();
    }

    /**
     * Checks if the given comparison operator is valid for the given filter type.
     *
     * @param  $filterType
     * @param  $operator
     *
     * @throws HandlingException
     */
    private static function isValidComparisonOperatorFor($filterType, $operator)
    {
        $validOperators = Operators::getValidComparisonOperators($filterType);

        if (! in_array($operator, $validOperators)) {
            throw HandlingException::instance('Invalid comparison operator.', compact('operator'));
        }
    }
}
