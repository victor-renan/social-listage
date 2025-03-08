<?php

namespace App\Models;

use App\Enums\SocialMediaOptions;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

class SocialMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'token',
        'additional_info',
        'user_id',
    ];

    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'type' => SocialMediaOptions::class,
            'additional_info' => 'array'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function validation(array $data): object
    {
        return (object) [
            'rules' => [
                'name' => ['required', Rule::unique('social_media')->ignore($data['id'] ?? null)],
                'type' => ['required', new EnumValue(SocialMediaOptions::class)],
                'user_id' => 'required',
                'additional_info' => 'array',
            ],
            'messages' => [
                'name.required' => 'Digite um nome',
                'name.unique' => 'Uma integração com este nome já existe',
                'type.required' => 'Escolha um tipo válido',
                'user_id.required' => 'Usuário criador faltando',
                'additional_info.array' => 'Este valor precisa ser um objeto válido',
            ]
        ];
    }
}
