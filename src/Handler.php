<?php

namespace DrupalLibraryMapping;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;

class Handler {
    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var array
     */
    protected $mapping = [];

    /**
     * Handler constructor.
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     */
    public function __construct(Composer $composer, IOInterface $io) {
        $this->composer = $composer;
        $this->io = $io;
        $this->fs = new Filesystem();
        $this->mapping = $this->getMapping();
    }

    public function getMapping() {
        $extra = $this->composer->getPackage()->getExtra();
        $mapping = (isset($extra['drupal-library-mapping']))
            ? $extra['drupal-library-mapping']
            : [];

        $repositoryManager = $this->composer->getRepositoryManager();
        $localRepository = $repositoryManager->getLocalRepository();
        $packages = $localRepository->getPackages();
        foreach ($packages as $package) {
            $extra = $package->getExtra();
            if (isset($extra['drupal-library-mapping'])) {
                $package_mapping = $extra['drupal-library-mapping'];
                $mapping = array_merge_recursive($mapping, $package_mapping);
            }
        }

        return $mapping;
    }

    public function moveMappedPackage(PackageInterface $package) {
        $types = ['bower-asset', 'npm-asset', 'drupal-library'];
        if (in_array($package->getType(), $types)) {
            $path = $this->composer->getInstallationManager()->getInstallPath($package);
            $name = $package->getName();
            if (array_key_exists($name, $this->mapping)) {
                $new_name = $this->mapping[$name];
                $this->io->write(sprintf("<info>Mapping library %s to %s</info>", $name, $new_name));
                $parent_path = dirname($path);
                $new_path = $parent_path . DIRECTORY_SEPARATOR . $new_name;
                $this->fs->removeDirectory($new_path);
                $this->fs->copyThenRemove($path, $new_path);
            }
        }
    }

    /**
     * @param \Composer\Package\PackageInterface $package
     */
    public function onPostPackageEvent(PackageInterface $package) {
        $this->moveMappedPackage($package);
    }
}
