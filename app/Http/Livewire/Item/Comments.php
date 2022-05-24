<?php

namespace App\Http\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use App\View\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class Comments extends Component implements HasForms
{
    use InteractsWithForms;

    public Item $item;
    public $comments;
    public $content;

    public function mount()
    {
        $this->form->fill([]);
    }

    public function submit()
    {
        if (!auth()->user()) {
            return redirect()->route('login');
        }

        $formState = array_merge($this->form->getState(), ['user_id' => auth()->id()]);

        $this->item->comments()->create($formState);

        $this->content = '';
    }

    protected function getFormSchema(): array
    {
        return [
            MarkdownEditor::make('content')
                ->helperText('You may use @ to mention someone.')
                ->required()
                ->minLength(3),
        ];
    }

    public function render()
    {
        $this->comments = $this->item->comments()->with('user:id,name,email')->oldest()->get();

        return view('livewire.item.comments');
    }
}
