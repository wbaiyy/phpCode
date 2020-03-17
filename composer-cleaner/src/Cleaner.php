<?php
namespace Wbaiyy\ComposerCleaner;

use Composer\IO\IOInterface;
use Composer\Util\Filesystem;

/**
 * 清理器
 */
class Cleaner
{
    /**
     * @var IOInterface
     */
    private $io;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * 构造函数
     *
     * @param IOInterface $io
     * @param Filesystem $filesystem
     */
    public function __construct(IOInterface $io, Filesystem $filesystem)
    {
        $this->io = $io;
        $this->filesystem = $filesystem;
    }

    /**
     * 清理
     *
     * @param string $vendorPath
     * @param array $extra
     */
    public function clean($vendorPath, $extra)
    {
        foreach ($this->getInstalledPackages($vendorPath) as $item) {
            if (!empty($item['name'])) {
                $this->cleanPackage($vendorPath . '/' . $item['name']);
            }
        }

        foreach ($extra as $item) {
            $item = $vendorPath . '/' . $item;
            if ($this->filesystem->remove($item)) {
                $this->io->write('Wbaiyy/composer-cleaner: removed ' . $item);
            }
        }
    }

    /**
     * 清理指定包
     *
     * @param string $packagePath
     * @return bool
     */
    private function cleanPackage($packagePath)
    {
        if (!is_file($file = $packagePath . '/.gitattributes')) {
            return false;
        }
        foreach ($this->parseGitAttributes(file($file)) as $item) {
            $item = $packagePath . '/' . ltrim($item, '/');
            if ($this->filesystem->remove($item)) {
                $this->io->write('Wbaiyy/composer-cleaner: removed ' . $item);
            }
        }
        return true;
    }

    /**
     * 获取已经安装的包
     *
     * @param string $vendorPath
     * @return array
     */
    private function getInstalledPackages($vendorPath)
    {
        if (!is_file($file = $vendorPath . '/composer/installed.json')) {
            $this->io->write(
                'Wbaiyy/composer-cleaner: Composer installed file [' . $file . '] does not exist'
            );
            return [];
        }
        return json_decode(file_get_contents($file), true);
    }

    /**
     * 解析`.gitattributes`
     *
     * @param array $lines
     * @return array
     */
    private function parseGitAttributes($lines)
    {
        $files = [];
        foreach ($lines as $item) {
            $item = trim($item);
            $item = preg_split('/\s+/', $item);
            if (2 === count($item)
                && 'export-ignore' === $item[1]
                && 0 !== strpos($item[0], '#')
            ) {
                $files[] = $item[0];
            }
        }
        return $files;
    }
}
