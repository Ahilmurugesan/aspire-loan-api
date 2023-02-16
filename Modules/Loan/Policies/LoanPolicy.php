<?php

namespace Modules\Loan\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Exceptions\UnAuthorizedException;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Loan  $loan
     * @return Response|bool
     * @throws UnAuthorizedException
     */
    public function view(User $user, Loan $loan): Response|bool
    {
        return ($user->id === $loan->user_id || $user->is_admin)
            ? Response::allow()
            : throw new UnAuthorizedException('You are not authorized to access');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Loan  $loan
     * @return Response|bool
     * @throws UnAuthorizedException
     */
    public function update(User $user, Loan $loan): Response|bool
    {
        return $user->id === $loan->user_id
            ? Response::allow()
            : throw new UnAuthorizedException('You are not authorized to access');
    }
}
