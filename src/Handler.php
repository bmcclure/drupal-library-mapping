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

    public function getMappingType() {
        $extra = $this->composer->getPackage()->getExtra();

        $types = ['symlink', 'copy', 'move'];

        $copy_type = (isset($extra['drupal-library-mapping-type']))
            ? $extra['drupal-library-mapping-type']
            : 'copy';

        if (!in_array($copy_type, $types)) {
            $copy_type = 'copy';
        }

        return $copy_type;
    }

    public function getMapping() {
        $this->io->write(sprintf("<info>Gathering Drupal library mapping</info>"));
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
                $mapping = array_merge($mapping, $package_mapping);
            }
        }

        return $mapping;
    }

    public function moveMappedPackage(PackageInterface $package) {
        $types = ['bower-asset', 'npm-asset', 'drupal-library'];
        if (in_array($package->getType(), $types)) {
            $path = $this->composer->getInstallationManager()->getInstallPath($package);
            $name = basename($path);
            if (array_key_exists($name, $this->mapping)) {
                $new_name = $this->mapping[$name];
                $parent_path = dirname($path);
                $new_path = $parent_path . DIRECTORY_SEPARATOR . $new_name;
                $this->fs->removeDirectory($new_path);
                $copy_type = $this->getMappingType();

                switch ($copy_type) {
                    case 'symlink':
                        $this->io->write(sprintf("<info>Symlinking library %s to %s</info>", $name, $new_name));
                        $base = realpath(getcwd()) . DIRECTORY_SEPARATOR;
                        $this->fs->relativeSymlink($base . $path, $base . $new_path);
                        break;
                    case 'copy':
                        $this->io->write(sprintf("<info>Copying library %s to %s</info>", $name, $new_name));
                        copy($path, $new_path);
                        break;
                    case 'move':
                        $this->io->write(sprintf("<info>Moving library %s to %s</info>", $name, $new_name));
                        $this->fs->copyThenRemove($path, $new_path);
                        break;
                }

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
