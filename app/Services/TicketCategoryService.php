<?php

namespace App\Services;

use App\Exceptions\ConflictException;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// app/Services/TicketCategoryService.php
// crud for ticket categories, quota never drops below sold
class TicketCategoryService
{
    public function store(array $data): TicketCategory
    {
        $data['sold'] = 0;

        return TicketCategory::query()->create($data);
    }

    public function update(TicketCategory $category, array $data): TicketCategory
    {
        return DB::transaction(function () use ($category, $data): TicketCategory {
            $locked = TicketCategory::query()->whereKey($category->id)->lockForUpdate()->firstOrFail();

            if (array_key_exists('quota', $data) && (int) $data['quota'] < $locked->sold) {
                throw ValidationException::withMessages(['quota' => ['Kuota tidak boleh lebih kecil dari jumlah tiket terjual ('.$locked->sold.').']]);
            }

            $locked->update($data);

            return $locked->refresh();
        }, 3);
    }

    public function delete(TicketCategory $category): void
    {
        if ($category->sold > 0 || $category->orderItems()->exists()) {
            throw new ConflictException('Kategori tiket sudah terpakai di pesanan sehingga tidak dapat dihapus.');
        }

        $category->delete();
    }
}
