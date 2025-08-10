<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\AddExpenseUseCase;
use App\Application\UseCase\AddMemberUseCase;
use App\Application\UseCase\CreateGroupUseCase;
use App\Application\UseCase\DeleteExpenseUseCase;
use App\Application\UseCase\RemoveMemberUseCase;
use App\Application\UseCase\ToggleSimplifyDebtsUseCase;
use App\Application\UseCase\UpdateExpenseUseCase;
use App\Application\UseCase\UpdateGroupUseCase;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\API\DTO\ExpenseDTO;
use App\Infrastrstructure\API\DTO\RequestGroupDTO;
use App\Infrastrstructure\API\Exceptions\UnauthorizedGroupActionException;
use App\Infrastrstructure\API\Exceptions\UserAlreadyInGroupException;
use App\Infrastrstructure\API\Resource\CustomerResource;
use App\Infrastrstructure\API\Resource\ExpenseResource;
use App\Infrastrstructure\API\Resource\GroupResource;
use App\Infrastrstructure\Mapper\GroupEloquentToDomainEntity;
use App\Models\Expense;
use App\Models\Group;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

readonly class GroupController
{
    /**
     * @param CreateGroupUseCase $createGroupUseCase
     * @param AddMemberUseCase $addMemberUseCase
     * @param UpdateGroupUseCase $updateGroupUseCase
     * @param AddExpenseUseCase $addExpenseUseCase
     * @param UpdateExpenseUseCase $updateExpenseUseCase
     * @param DeleteExpenseUseCase $deleteExpenseUseCase
     * @param RemoveMemberUseCase $removeMemberUseCase
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     * @param ToggleSimplifyDebtsUseCase $toggleSimplifyDebtsUseCase
     * @param CustomerReadRepositoryInterface $customerReadRepository
     */
    public function __construct(
        public CreateGroupUseCase $createGroupUseCase,
        public AddMemberUseCase $addMemberUseCase,
        public UpdateGroupUseCase $updateGroupUseCase,
        public AddExpenseUseCase $addExpenseUseCase,
        public UpdateExpenseUseCase $updateExpenseUseCase,
        public DeleteExpenseUseCase $deleteExpenseUseCase,
        public RemoveMemberUseCase $removeMemberUseCase,
        public GroupReadRepositoryInterface $groupReadRepository,
        public GroupWriteRepositoryInterface $groupWriteRepository,
        public ToggleSimplifyDebtsUseCase $toggleSimplifyDebtsUseCase,
        public CustomerReadRepositoryInterface $customerReadRepository
    ) {}

    /**
     * @return AnonymousResourceCollection
     */
    public function list(): AnonymousResourceCollection
    {
        return GroupResource::collection($this->groupReadRepository->list(auth()->id()));
    }

    /**
     * @param RequestGroupDTO $groupDTO
     *
     * @return Response
     */
    public function createGroup(RequestGroupDTO $groupDTO): Response
    {
        $groupDTO->created_by = auth()->id();

        $group = $this->createGroupUseCase->execute($groupDTO);

        return response(new GroupResource($this->groupReadRepository->findById($group->id)),
            ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Group $group
     * @param RequestGroupDTO $groupDTO
     *
     * @return JsonResponse
     */
    public function updateGroup(Group $group, RequestGroupDTO $groupDTO): JsonResponse
    {
        $this->updateGroupUseCase->execute($group->id, $groupDTO);

        return response()->json(['message' => 'Group updated successfully'], ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Group $group
     *
     * @return JsonResponse
     */
    public function toggleSimplify(Group $group): JsonResponse
    {
        $this->toggleSimplifyDebtsUseCase->execute($group->id);

        return response()->json(['message' => 'Group updated successfully'], ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Group $group
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addMember(Group $group, Request $request): JsonResponse
    {
        try {
            $this->addMemberUseCase->execute($group->id, $request->user()->id);

            return response()->json(['message' => 'Member added successfully'], ResponseAlias::HTTP_CREATED);
        } catch (UserAlreadyInGroupException $e) {
            return response()->json(['error' => $e->getMessage()], ResponseAlias::HTTP_CONFLICT);
        }
    }

    /**
     * @param ExpenseDTO $expenseDTO
     * @param Group $group
     *
     * @return Response|JsonResponse
     */
    public function addExpense(ExpenseDTO $expenseDTO, Group $group): Response|JsonResponse
    {
        try {
            $this->addExpenseUseCase->execute($expenseDTO, $group->id, auth()->id());
        } catch (UnauthorizedGroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], ResponseAlias::HTTP_FORBIDDEN);
        }

        return response(new GroupResource($this->groupReadRepository->findById($group->id)), ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param ExpenseDTO $expenseDTO
     * @param Group $group
     * @param Expense $expense
     *
     * @return Response|JsonResponse
     */
    public function updateExpense(ExpenseDTO $expenseDTO, Group $group, Expense $expense): Response|JsonResponse
    {
        try {
            $expense = $this->updateExpenseUseCase->execute($expenseDTO, $group->id, $expense->id, auth()->id());
        } catch (UnauthorizedGroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], ResponseAlias::HTTP_FORBIDDEN);
        }

        return response(
            [
                'group'   => new GroupResource($this->groupReadRepository->findById($group->id)),
                'expense' => new ExpenseResource($expense),
            ],
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * @param Group $group
     * @param Expense $expense
     *
     * @return Response
     */
    public function deleteExpense(Group $group, Expense $expense): Response
    {
        $this->deleteExpenseUseCase->execute($group->id, $expense->id, auth()->id());

        return response(new GroupResource($this->groupReadRepository->findById($group->id)), ResponseAlias::HTTP_OK);
    }

    /**
     * @param Group $group
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeMember(Group $group, Request $request): JsonResponse
    {
        try {
            $this->removeMemberUseCase->execute($request->user()->id, $group->id, $request->input('ids'));
        } catch (UnauthorizedGroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], ResponseAlias::HTTP_FORBIDDEN);
        }

        return response()->json(['message' => 'Member removed successfully'], ResponseAlias::HTTP_OK);
    }

    /**
     * @param Group $group
     *
     * @return AnonymousResourceCollection
     */
    public function members(Group $group): AnonymousResourceCollection
    {
        return CustomerResource::collection($this->groupReadRepository->members($group->id));
    }

    /**
     * @param Group $group
     *
     * @return GroupResource
     */
    public function view(Group $group): GroupResource
    {
        return new GroupResource($this->groupReadRepository->findById($group->id));
    }

    /**
     * @param Group $group
     *
     * @return JsonResponse
     */
    public function delete(Group $group): JsonResponse
    {
        try {
            GroupEloquentToDomainEntity::toEntity($group)->remove();
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'Group deleted successfully'], ResponseAlias::HTTP_OK);
    }

    /**
     * @param Group $group
     *
     * @return JsonResponse
     */
    public function uploadAvatar(Group $group): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], ResponseAlias::HTTP_BAD_REQUEST);
        }

        $file = request()->file('avatar');

        $path = $file->storeAs('groups/avatars', $group->id.'.'.$file->extension());

        $group = $this->groupReadRepository->findById($group->id);

        $group->avatar = $path;

        $this->groupWriteRepository->save($group);

        return response()->json(['path' => Storage::url($path)]);
    }
}
