<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRequest;
use App\Todo;
use App\Http\Resources\TodoCollection;
use Illuminate\Http\Request;
use App\Http\Resources\Todo as TodoResource;
use Symfony\Component\HttpFoundation\Response;


class TodoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/todos",
     *     operationId="getTodoList",
     *     tags={"Todos"},
     *     summary="Display a listing of the resource",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(ref="#/components/schemas/ModelTodo"),
     *         @OA\XmlContent(ref="#/components/schemas/ModelTodo")
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new TodoCollection(Todo::all());
    }

    /**
     * @OA\Post(
     *     path="/todo/create",
     *     operationId="todoCreate",
     *     tags={"Todos"},
     *     summary="Create yet another Todo list",
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Create Todo Description",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="varchar255",
     *         )
     *     ),
     *     security={
     *       {"app_id": {123}},
     *     },
     *     @OA\Response(
     *         response="201",
     *         description="Everything is fine",
     *         @OA\JsonContent(ref="#/components/schemas/ModelTodo"),
     *         @OA\XmlContent(ref="#/components/schemas/ModelTodo")
     *     ),
     * )
     * Store a newly created resource in storage.
     *
     * @param App\Http\Requests\TodoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TodoRequest $request)
    {
        $todo = Todo::create($request->all());
        return (new TodoResource($todo))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * @OA\Put(
     *      path="/todo/update",
     *      operationId="updateTodoDesription",
     *      tags={"Todos"},
     *      summary="Update existing Todos Description",
     *      description="Returns updated Todo data",
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id from Todo",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int63",
     *         )
     *      ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Update Todo Description",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="varchar255",
     *         )
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ModelTodo"),
     *          @OA\XmlContent(ref="#/components/schemas/ModelTodo")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'description' => 'string|max:255|nullable',
        ]);

        $todo = Todo::findOrFail($request->id);
        $todo->description = $request->description;
        $todo->update();

        return (new TodoResource($todo))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @OA\Put(
     *      path="/todo/complete",
     *      operationId="changeStatusTodoComplete",
     *      tags={"Todos"},
     *      summary="Update existing Todos Status Complete",
     *      description="Returns updated Todo data",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"completedIds","completed"},
     *            @OA\Property(property="completedIds", format="array", example="[1,2,3]"),
     *            @OA\Property(property="completed", format="integer", example="1"),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ModelTodo")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function complete(Request $request)
    {
        $request->validate([
            'completedIds' => 'required|array',
            'completed' => 'required',
        ]);

        $todos = Todo::whereIn('id', $request->completedIds)->update(['completed' => $request->completed]);
        $message = $todos . ($todos > 1 ? ' Todos' : ' Todo') . " Successfully Updated";
        return response()->json($message, Response::HTTP_ACCEPTED);
    }

    /**
     * @OA\Delete(
     *      path="/todo/delete/{id}",
     *      operationId="deleteOneTodo",
     *      tags={"Todos"},
     *      summary="Delete existing Todo",
     *      description="Deletes a record and returns no content, reuired parameter completed ids todos an array",
     *      @OA\Parameter(
     *          name="id",
     *          description="Todo id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function delete(Request $request, $id = null)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Delete(
     *      path="/todos/delete-completed",
     *      operationId="deleteTodosCompleted",
     *      tags={"Todos"},
     *      summary="Delete existing Completed todos",
     *      description="Deletes a record and returns no content",
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity, The given data was invalid."
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"selectedIds"},
     *            @OA\Property(property="selectedIds", format="array", example="[1,2,3]")
     *         ),
     *     ),
     * )
     */
    public function deleteCompleted(Request $request)
    {

        $request->validate([
            'selectedIds' => 'required|array',
        ]);

        $ids = $request->selectedIds;

        $todos = Todo::findOrFail($ids);
        foreach ($todos as $todo) {
            $todo->delete();
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
