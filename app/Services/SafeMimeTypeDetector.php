<?php

namespace App\Services;

use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;

class SafeMimeTypeDetector implements MimeTypeDetector
{
    protected $detector;

    public function __construct()
    {
        // Try to use finfo if available, otherwise fallback to extension-based detection
        try {
            if (class_exists('finfo') && function_exists('finfo_open')) {
                $this->detector = new FinfoMimeTypeDetector();
            } else {
                $this->detector = new ExtensionMimeTypeDetector();
            }
        } catch (\Exception $e) {
            // If finfo fails, use extension-based detection
            $this->detector = new ExtensionMimeTypeDetector();
        }
    }

    public function detectMimeType(string $path, $contents): ?string
    {
        try {
            return $this->detector->detectMimeType($path, $contents);
        } catch (\Exception $e) {
            // If detection fails, fallback to extension-based detection
            $fallback = new ExtensionMimeTypeDetector();
            return $fallback->detectMimeType($path, $contents);
        }
    }

    public function detectMimeTypeFromBuffer(string $contents): ?string
    {
        try {
            return $this->detector->detectMimeTypeFromBuffer($contents);
        } catch (\Exception $e) {
            // If detection fails, fallback to extension-based detection
            $fallback = new ExtensionMimeTypeDetector();
            return $fallback->detectMimeTypeFromBuffer($contents);
        }
    }

    public function detectMimeTypeFromPath(string $path): ?string
    {
        try {
            return $this->detector->detectMimeTypeFromPath($path);
        } catch (\Exception $e) {
            // If detection fails, fallback to extension-based detection
            $fallback = new ExtensionMimeTypeDetector();
            return $fallback->detectMimeTypeFromPath($path);
        }
    }

    public function detectMimeTypeFromFile(string $path): ?string
    {
        try {
            return $this->detector->detectMimeTypeFromFile($path);
        } catch (\Exception $e) {
            // If detection fails, fallback to extension-based detection
            $fallback = new ExtensionMimeTypeDetector();
            return $fallback->detectMimeTypeFromFile($path);
        }
    }
}

