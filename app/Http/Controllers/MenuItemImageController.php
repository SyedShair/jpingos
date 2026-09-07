<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemImageController extends Controller
{
    /**
     * Dropzone target for the CREATE form, where the menu item doesn't
     * exist yet. Files land in a per-draft temp folder and are attached
     * to the item once the main form is submitted (see
     * MenuItemController::store()).
     */
    public function storeTemp(Request $request, string $draftToken)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:4096'],
        ]);

        $this->assertValidToken($draftToken);

        $path = $request->file('file')->store("menu-items/temp/{$draftToken}", 'public');

        return response()->json([
            'path'     => $path,
            'filename' => basename($path),
            'url'      => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Remove a temp image before the item has been created.
     */
    public function destroyTemp(Request $request, string $draftToken, string $filename)
    {
        $this->assertValidToken($draftToken);

        $path = "menu-items/temp/{$draftToken}/{$filename}";

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return response()->json(['deleted' => true]);
    }

    /**
     * Dropzone target for the EDIT form, where the item already exists —
     * uploads are saved immediately as real gallery images.
     */
    public function store(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('file')->store("menu-items/{$menuItem->id}", 'public');

        $isFirstImage = $menuItem->images()->count() === 0;

        $image = $menuItem->images()->create([
            'path'       => $path,
            'sort_order' => $menuItem->images()->max('sort_order') + 1,
            'is_primary' => $isFirstImage, // first upload becomes the cover automatically
        ]);

        return response()->json([
            'id'         => $image->id,
            'url'        => $image->url,
            'is_primary' => $image->is_primary,
        ]);
    }

    /**
     * Delete a real gallery image. If it was the cover image, promote
     * the next one automatically so the dish never ends up coverless
     * while other images remain.
     */
    public function destroy(MenuItemImage $image)
    {
        $menuItem = $image->menuItem;
        $wasPrimary = $image->is_primary;

        Storage::disk('public')->delete($image->path);
        $image->delete();

        if ($wasPrimary) {
            $next = $menuItem->images()->orderBy('sort_order')->first();
            $next?->update(['is_primary' => true]);
        }

        return response()->json(['deleted' => true]);
    }

    /**
     * Set an image as the dish's cover/primary photo.
     */
    public function makePrimary(MenuItemImage $image)
    {
        $image->menuItem->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return response()->json(['ok' => true]);
    }

    /**
     * Persist new drag-and-drop order from the edit page.
     * Expects: { order: [imageId, imageId, ...] }
     */
    public function reorder(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:menu_item_images,id'],
        ]);

        foreach ($data['order'] as $index => $imageId) {
            MenuItemImage::where('id', $imageId)
                ->where('menu_item_id', $menuItem->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Draft tokens are just UUIDs used to namespace temp uploads — this
     * blocks path traversal via a malformed token in the URL.
     */
    protected function assertValidToken(string $token): void
    {
        abort_unless(Str::isUuid($token), 422, 'Invalid draft token.');
    }
}
