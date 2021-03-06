<?php namespace Modules\Notification\Requests;

use Modules\Notification\Models\Notification;
use Illuminate\Foundation\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class UpdateNotificationRequest extends FormRequest
{
    use HasWithParameter;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return  bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules()
    {
        $rules = Notification::$rules;
        return $rules;
    }

    public function attributes()
    {
        return __('notification.fields');
    }
}
