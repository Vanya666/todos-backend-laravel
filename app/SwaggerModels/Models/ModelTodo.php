<?php


namespace App\SwaggerModels;

/**
 * @OA\Schema(
 *     description="Some simple request createa as Todos",
 *     type="object",
 *     title="Todo",
 * )
 */
class ModelTodo
{

    /**
     * @OA\Property(
     *     title="Id",
     *     description="Some integer field",
     *     format="int63",
     *     example="123456"
     * )
     *
     * @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Description",
     *     description="Some text field",
     *     format="string",
     *     example="Make routing list"
     * )
     *
     * @var string
     */
    public $description;

    /**
     * @OA\Property(
     *     title="Completed",
     *     description="Or zero or one",
     *     format="tinyinteger",
     *     default="0"
     * )
     *
     * @var int
     */
    public $completed;
}
