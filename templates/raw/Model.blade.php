<?php
        use zgldh\Scaffold\Installer\Utils;
    /**
     * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
     */
    $fillableFields = [];
    foreach($MODEL->getFields() as $field)
    {
        $fillableFields[] = $field->getName();
    }

    $casts = [];
    foreach($MODEL->getFields() as $field)
    {
        $casts[$field->getName()] = $field->getCastType();
    }

    $rules = [];
    foreach($MODEL->getFields() as $field)
    {
        $rules[$field->getName()] = $field->getRule();
    }
    echo '<?php' ?> namespace {{$NAME_SPACE}}\Models;

use Illuminate\Database\Eloquent\Model;
@if($MODEL->isActivityLog())
use Spatie\Activitylog\Traits\LogsActivity;
@endif
@if($MODEL->isSoftDelete())
use Illuminate\Database\Eloquent\SoftDeletes;
@endif

class {{$MODEL_NAME}} extends Model
{
@if($MODEL->isActivityLog())
    use LogsActivity;
@endif
@if($MODEL->isSoftDelete())
    use SoftDeletes;
@endif

    public $table = '{{$MODEL->getTable()}}';

    public $fillable = <?php echo Utils::exportArray($fillableFields);?>;
@if($MODEL->isActivityLog())

    protected static $logAttributes = <?php echo Utils::exportArray($fillableFields);?>;
@endif

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = <?php echo Utils::exportArray($casts);?>;
@if($MODEL->isSoftDelete())
    protected $dates = ['deleted_at'];
@endif

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = <?php echo Utils::exportArray($rules);?>;
<?php
    foreach($MODEL->getFields() as $field){
        $relationship = $field->getRelationship();
        if(!$relationship){
            continue;
        }
        $relationshipType = $relationship['type'];
        unset($relationship['type']);
        $relationshipClassName = ucfirst(camel_case($relationshipType));
        $relatedName = camel_case(basename($relationship[0]));
        $relationParams = array_reduce($relationship, function($carry, $param){
            return $carry?$carry.", '{$param}'":"'{$param}'";
        },null);
        ?>
    /**
     * @return \Illuminate\Database\Eloquent\Relations\{{$relationshipClassName}}
     **/
    public function {{$relatedName}}()
    {
        return $this->{{$relationshipType}}({!! $relationParams !!});
    }
<?php }?>

}
