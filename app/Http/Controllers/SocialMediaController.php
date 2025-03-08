<?php

namespace App\Http\Controllers;

use App\Enums\SocialMediaOptions;
use App\Models\SocialMedia;
use App\Strategies\FacebookStrategy;
use App\Strategies\InstagramStrategy;
use App\Strategies\LinkedinStrategy;
use App\Strategies\SocialMediaStrategy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialMediaController extends Controller
{
    public ?SocialMediaStrategy $socialMediaStrategy = null;

    private function setIntegrationStrategy(?SocialMediaStrategy $strategy)
    {
        $this->socialMediaStrategy = $strategy;
    }

    private function matchIntegrationStrategy(?string $option)
    {
        $this->setIntegrationStrategy(
            match ($option) {
                SocialMediaOptions::Facebook => new FacebookStrategy(),
                SocialMediaOptions::Instagram => new InstagramStrategy(),
                SocialMediaOptions::Linkedin => new LinkedinStrategy(),
                default => null,
            }
        );
    }

    /**
     * Retrieves social media implementations
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $instances = SocialMedia::query();

        if ($request->type) {
            $instances->where('type', $request->type);
        }

        return response()->json($instances->get());
    }

    public function details(Request $request)
    {
        $instance = SocialMedia::where(['id' => $request->id])->first();

        if (!$instance) {
            return $this->notFoundResponse();
        }

        return response()->json($instance);
    }

    /**
     * Retrieves implementations based on the query param `type`
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function posts(Request $request)
    {
        $instance = SocialMedia::where(['id' => $request->id])->first();

        if (!$instance) {
            return $this->notFoundResponse();
        }

        $this->matchIntegrationStrategy($instance->type);

        return $this->socialMediaStrategy->posts($instance);
    }

    public function create(Request $request)
    {
        $this->matchIntegrationStrategy($request->type);

        if (!$this->socialMediaStrategy) {
            return $this->notSupportedMedia();
        }

        return $this->save($request->all());
    }

    public function update(Request $request)
    {
        $instance = SocialMedia::where(['id' => $request->id])->first();

        if (!$instance) {
            return $this->notFoundResponse();
        }

        $this->matchIntegrationStrategy($instance->type);

        return $this->save(
            array_merge(
                $instance->makeVisible('token')->toArray(),
                $request->except('type'),
            )
        );
    }

    public function delete(Request $request)
    {
        $instance = SocialMedia::where(['id' => $request->id])->first();

        if (!$instance) {
            return $this->notFoundResponse();
        }

        $this->matchIntegrationStrategy($instance->type);

        try {
            \DB::transaction(function () use ($instance) {
                $instance->delete();
            });

            return response()->json([
                'message' => 'Item deletado com sucesso',
            ]);

        } catch (\Exception $e) {

            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Falha ao deletar implementação'
            ], 500);
        }
    }

    public function save(array $data)
    {
        $validation = $this->socialMediaStrategy->validate($data);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Falha ao validar dados',
                'errors' => $validation->errors(),
            ], 400);
        }

        try {
            $instance = new SocialMedia($data);

            if (isset($data['id'])) {
                $instance = SocialMedia::find($data['id'])->fill($data);
            }

            \DB::transaction(function () use ($instance) {
                $instance->save();
            });

            return response()->json([
                'message' => 'Plataforma salva com sucesso',
                'data' => $instance->toArray(),
            ]);

        } catch (\Exception $e) {

            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Falha ao salvar os dados'
            ], 500);
        }
    }

    private function notFoundResponse()
    {
        return response()->json([
            'message' => 'Nenhuma implementação encontrada'
        ], 404);
    }

    private function notSupportedMedia()
    {
        return response()->json([
            'message' => 'Rede social não suportada',
            'avaliable_options' => array_values(SocialMediaOptions::asArray()),
        ], 400);
    }
}
