use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SlotAvailabilityController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check-slot-availability/{timeSlot}', [SlotAvailabilityController::class, 'check']);
}); 