<?php namespace zgldh\Scaffold\Installer;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
abstract class ModuleInstaller
{
    private $moduleDirectoryName = null;
    protected $moduleTemplatePath = null;
    protected $dynamicVariables = [];

    public function __construct()
    {
        $this->moduleDirectoryName = config('zgldh-scaffold.modules', 'Modules');
        $this->dynamicVariables = [
            'NAME' => $this->moduleDirectoryName
        ];
    }

    protected function moduleRootNamespace()
    {
        return $this->moduleDirectoryName;
    }

    /**
     * 得到当前Module的模板文件夹下某文件路径
     * @param $path
     * @return string
     */
    protected function getModuleTemplatePath($path)
    {
        $path = $this->moduleTemplatePath . $path;
        return $path;
    }

    /**
     * 得到当前Module的模板文件夹下某文件内容
     * @param $path
     * @return string
     */
    protected function getModuleTemplateContent($path)
    {
        $path = $this->getModuleTemplatePath($path);
        $content = file_get_contents($path);
        return $content;
    }

    abstract public function run();

    /**
     * 将本 Module 的 template/module 文件夹下内容复制到位。
     * @param $targetModuleName
     */
    protected function copyModuleFilesTo($targetModuleName)
    {
        $src = $this->getModuleTemplatePath('module');
        $dst = base_path($this->moduleDirectoryName . '/' . $targetModuleName);
        Utils::copy($src, $dst, $this->dynamicVariables);
    }

    /**
     * 添加 ServiceProvider
     * @param $targetModuleName     string
     * @param $serviceProviderClass string
     */
    protected function addServiceProvider($targetModuleName, $serviceProviderClass)
    {
        $serviceProvider = '\\' . $this->moduleDirectoryName . '\\' . $targetModuleName . '\\' . $serviceProviderClass;
        Utils::addServiceProvider($serviceProvider);
    }

    /**
     * 添加 PHP Route
     * @param $targetModuleName string
     */
    protected function addRoute($targetModuleName)
    {
        $routeFile = "require base_path('{$this->moduleDirectoryName}/{$targetModuleName}/routes.php');";
        Utils::addRoute($routeFile);
    }

    /**
     * 添加 PHP Route
     * @param $targetModuleName string
     */
    protected function addToVueRoute($targetModuleName)
    {
        $routeLine = "require('{$this->moduleDirectoryName}/{$targetModuleName}/resources/assets/routes.js').default";
        Utils::addToVueRoute($routeLine);
    }

    protected function publishMigration($className, $filePath)
    {
        if (!class_exists($className)) {
            // Publish the migration
            sleep(1);
            $timestamp = date('Y_m_d_His', time());
            $destPath = database_path('/migrations/' . $timestamp . '_' . basename($filePath) . '.php');
            Utils::copy($filePath, $destPath, $this->dynamicVariables);
        }
    }
}