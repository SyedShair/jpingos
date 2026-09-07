<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        return view('sliders.index', [
            'sliders' => Slider::ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('sliders.create', [
            'slider' => new Slider(),
        ]);
    }

    public function store(SliderRequest $request)
    {
        $data = $request->safe()->except('image');
        $data['image_path'] = $request->file('image')->store('sliders', 'public');
        $data['sort_order'] = $data['sort_order'] ?? (Slider::max('sort_order') + 1);

        Slider::create($data);

        return redirect()
            ->route('sliders.index')
            ->with('status', 'Slide added.');
    }

    public function edit(Slider $slider)
    {
        return view('sliders.edit', compact('slider'));
    }

    public function update(SliderRequest $request, Slider $slider)
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image_path);
            $data['image_path'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        return redirect()
            ->route('sliders.index')
            ->with('status', 'Slide updated.');
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('public')->delete($slider->image_path);
        $slider->delete();

        return redirect()
            ->route('sliders.index')
            ->with('status', 'Slide removed.');
    }

    public function toggleActive(Slider $slider)
    {
        $slider->update(['is_active' => ! $slider->is_active]);

        return response()->json(['is_active' => $slider->is_active]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:sliders,id'],
        ]);

        foreach ($request->input('order') as $index => $id) {
            Slider::whereKey($id)->update(['sort_order' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }
}