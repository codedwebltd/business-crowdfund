    // Documentation Routes (add inside admin prefix group)
    Route::get('/documentation', [App\Http\Controllers\Admin\DocumentationController::class, 'index'])->name('admin.documentation.index');
    Route::get('/documentation/{slug}', [App\Http\Controllers\Admin\DocumentationController::class, 'show'])->name('admin.documentation.show');
