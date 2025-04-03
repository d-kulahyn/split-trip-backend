<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\AddExpenseUseCase;
use App\Application\UseCase\AddMemberUseCase;
use App\Application\UseCase\CreateGroupUseCase;
use App\Application\UseCase\RemoveMemberUseCase;
use App\Application\UseCase\ToggleSimplifyDebtsUseCase;
use App\Application\UseCase\UpdateGroupUseCase;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\API\DTO\ExpenseDTO;
use App\Infrastrstructure\API\DTO\RequestGroupDTO;
use App\Infrastrstructure\API\Exceptions\UnauthorizedGroupActionException;
use App\Infrastrstructure\API\Exceptions\UserAlreadyInGroupException;
use App\Infrastrstructure\API\Resource\CustomerResource;
use App\Infrastrstructure\API\Resource\ExpenseResource;
use App\Infrastrstructure\API\Resource\GroupResource;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

readonly class GroupController
{
    /**
     * @param CreateGroupUseCase $createGroupUseCase
     * @param AddMemberUseCase $addMemberUseCase
     * @param UpdateGroupUseCase $updateGroupUseCase
     * @param AddExpenseUseCase $addExpenseUseCase
     * @param RemoveMemberUseCase $removeMemberUseCase
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     * @param ToggleSimplifyDebtsUseCase $toggleSimplifyDebtsUseCase
     */
    public function __construct(
        public CreateGroupUseCase $createGroupUseCase,
        public AddMemberUseCase $addMemberUseCase,
        public UpdateGroupUseCase $updateGroupUseCase,
        public AddExpenseUseCase $addExpenseUseCase,
        public RemoveMemberUseCase $removeMemberUseCase,
        public GroupReadRepositoryInterface $groupReadRepository,
        public GroupWriteRepositoryInterface $groupWriteRepository,
        public ToggleSimplifyDebtsUseCase $toggleSimplifyDebtsUseCase,
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

        return response(new GroupResource($this->groupReadRepository->findById($group->id)), 201);
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

        return response()->json(['id' => 'Group updated successfully'], ResponseAlias::HTTP_CREATED);
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
            $expense = $this->addExpenseUseCase->execute($expenseDTO, $group->id, auth()->id());
        } catch (UnauthorizedGroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], ResponseAlias::HTTP_FORBIDDEN);
        }

        return response(new ExpenseResource($expense), ResponseAlias::HTTP_CREATED);
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
    public function uploadAvatar(Group $group): JsonResponse
    {
        // Validate the request
        request()->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = request()->file('avatar');

        $path = $file->storeAs('groups/avatars', $group->id.'.'.$file->extension());

        $group = $this->groupReadRepository->findById($group->id);

        $group->avatar = $path;

        $this->groupWriteRepository->save($group);

        return response()->json(['path' => Storage::url($path)]);
    }
}
