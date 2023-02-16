<?php

namespace Modules\Loan\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Modules\Repay\Http\Resources\LoanRepaymentResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'user'          => $this->user->name,
            'amount'        => $this->amount,
            'period'        => $this->period,
            'status'        => $this->status,
            'analyst'       => $this->analyst->name ?? null,
            'comments'      => $this->comments,
            'repayments'    => LoanRepaymentResource::collection($this->whenLoaded('repayments'))
        ];
    }
}
