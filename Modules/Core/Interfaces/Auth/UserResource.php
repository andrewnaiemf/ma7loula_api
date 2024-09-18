<?php
 
 namespace Modules\Core\Interfaces\Auth;
 
use Illuminate\Http\Request;

interface UserResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array;
}