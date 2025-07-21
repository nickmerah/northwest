<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->log_id,
            'surname' => $this->log_surname,
            'firstname' => $this->log_firstname,
            'othernames' => $this->log_othernames,
            'applicationNumber' => $this->log_username,
            'email' => $this->log_email,
            'phoneNumber' => $this->log_gsm,
        ];
    }
}
