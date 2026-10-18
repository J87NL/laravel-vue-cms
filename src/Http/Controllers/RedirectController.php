<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Actions\ImportRedirects;
use J87NL\Cms\Actions\SaveRedirect;
use J87NL\Cms\Enums\RedirectSource;
use J87NL\Cms\Models\Redirect;
use J87NL\VueCms\Forms\RedirectForm;
use J87NL\VueCms\Forms\RedirectImportForm;
use J87NL\VueCms\Tables\RedirectTable;
use TranquilTools\FormBuilder\FormConfig;

class RedirectController extends Controller
{
    private const int MAX_REPORTED_ERRORS = 10;

    public function index(): Response
    {
        return Inertia::render($this->page('redirects_index'), [
            'redirects' => RedirectTable::build(),
            'createUrl' => route('cms.redirects.create'),
            'importUrl' => route('cms.redirects.import'),
            'breadcrumbs' => $this->breadcrumbs(),
        ]);
    }

    public function create(): Response
    {
        return $this->form(__('vue-cms::redirects.create.title'), route('cms.redirects.create'), RedirectForm::make()
            ->action(route('cms.redirects.store'))
            ->method('POST')
            ->fill([
                'status_code' => '301',
            ]));
    }

    public function store(Request $request, SaveRedirect $saveRedirect): RedirectResponse
    {
        $this->save(new Redirect, RedirectForm::make()->validate($request), $saveRedirect);

        $this->toast(__('vue-cms::redirects.flash.saved'));

        return to_route('cms.redirects.index');
    }

    public function edit(Redirect $redirect): Response
    {
        return $this->form(__('vue-cms::redirects.edit.title'), route('cms.redirects.edit', $redirect), RedirectForm::make()
            ->action(route('cms.redirects.update', $redirect))
            ->method('PUT')
            ->fill([
                'from_path' => $redirect->fromUrl(),
                'to' => $redirect->to,
                'status_code' => (string) $redirect->status_code,
            ]));
    }

    public function update(Request $request, Redirect $redirect, SaveRedirect $saveRedirect): RedirectResponse
    {
        $this->save($redirect, RedirectForm::make()->validate($request), $saveRedirect);

        $this->toast(__('vue-cms::redirects.flash.saved'));

        return to_route('cms.redirects.index');
    }

    public function destroy(Redirect $redirect): RedirectResponse
    {
        $redirect->delete();

        $this->toast(__('vue-cms::redirects.flash.deleted'));

        return to_route('cms.redirects.index');
    }

    public function importForm(): Response
    {
        return Inertia::render($this->page('form'), [
            'title' => __('vue-cms::redirects.import.title'),
            'description' => __('vue-cms::redirects.import.description'),
            'breadcrumbs' => [
                ...$this->breadcrumbs(),
                [
                    'title' => __('vue-cms::redirects.import.title'),
                    'href' => route('cms.redirects.import'),
                ],
            ],
            'backUrl' => route('cms.redirects.index'),
            'form' => RedirectImportForm::make()
                ->action(route('cms.redirects.import.store'))
                ->method('POST'),
        ]);
    }

    public function import(Request $request, ImportRedirects $importRedirects): RedirectResponse
    {
        RedirectImportForm::make()->validate($request);

        $file = $request->file('file');

        if (! $file instanceof UploadedFile) {
            abort(422);
        }

        $result = $importRedirects->handle($file->getRealPath());

        $this->toast(trans_choice('vue-cms::redirects.flash.imported', $result->imported, [
            'count' => $result->imported,
        ]));

        if ($result->errors === []) {
            return to_route('cms.redirects.index');
        }

        $lines = array_slice($result->errors, 0, self::MAX_REPORTED_ERRORS, true);

        return to_route('cms.redirects.index')->withErrors([
            'import' => __('vue-cms::redirects.import.failed_lines', [
                'count' => count($result->errors),
                'lines' => implode('; ', array_map(
                    fn (int $line, string $message): string => sprintf('%d: %s', $line, $message),
                    array_keys($lines),
                    $lines,
                )),
            ]),
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function save(Redirect $redirect, array $validated, SaveRedirect $saveRedirect): void
    {
        $saveRedirect->handle(
            $redirect,
            (string) $validated['from_path'],
            (string) $validated['to'],
            (int) $validated['status_code'],
            RedirectSource::Manual,
        );
    }

    private function form(string $title, string $url, FormConfig $form): Response
    {
        return Inertia::render($this->page('form'), [
            'title' => $title,
            'description' => __('vue-cms::redirects.index.description'),
            'breadcrumbs' => [
                ...$this->breadcrumbs(),
                [
                    'title' => $title,
                    'href' => $url,
                ],
            ],
            'backUrl' => route('cms.redirects.index'),
            'form' => $form,
        ]);
    }

    /**
     * @return list<array{title: string, href: string}>
     */
    private function breadcrumbs(): array
    {
        return [
            [
                'title' => __('vue-cms::redirects.index.title'),
                'href' => route('cms.redirects.index'),
            ],
        ];
    }
}
