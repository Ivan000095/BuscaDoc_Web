<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    /**
     * Directorio por defecto para almacenar archivos
     */
    private string $defaultDirectory = "uploads";

    /**
     * Extensiones de archivos permitidas para imágenes
     */
    private array $allowedImageExtensions = [
        "jpg",
        "jpeg",
        "png",
        "gif",
        "webp",
        "svg",
    ];

    /**
     * Tamaño máximo de archivo en bytes (5MB por defecto)
     */
    private int $maxFileSize = 5242880; // 5MB

    /**
     * Subir un archivo al storage
     *
     * @param UploadedFile $file
     * @param string|null $directory
     * @param string|null $filename
     * @return array
     * @throws \Exception
     */
    public function upload(
        UploadedFile $file,
        ?string $directory = null,
        ?string $filename = null,
    ): array {
        try {
            Log::info("FileService: Starting file upload process");
            Log::info("FileService: File details", [
                "original_name" => $file->getClientOriginalName(),
                "size" => $file->getSize(),
                "mime" => $file->getMimeType(),
                "valid" => $file->isValid(),
                "error" => $file->getError(),
            ]);

            // Validar el archivo
            $this->validateFile($file);
            Log::info("FileService: File validation passed");

            // Determinar el directorio de destino
            $uploadDirectory = $directory ?? $this->defaultDirectory;
            Log::info("FileService: Upload directory: " . $uploadDirectory);

            // Generar nombre único si no se proporciona
            if (!$filename) {
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . "." . $extension;
            }
            Log::info("FileService: Generated filename: " . $filename);

            // Verificar que el directorio exista
            $fullPath = storage_path("app/public/" . $uploadDirectory);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
                Log::info("FileService: Created directory: " . $fullPath);
            }

            // Almacenar el archivo
            $path = $file->storeAs($uploadDirectory, $filename, "public");
            Log::info("FileService: File stored at path: " . $path);

            $result = [
                "success" => true,
                "path" => $path,
                "url" => Storage::url($path),
                "filename" => $filename,
                "original_name" => $file->getClientOriginalName(),
                "size" => $file->getSize(),
                "mime_type" => $file->getMimeType(),
            ];

            Log::info("FileService: Upload completed successfully", $result);
            return $result;
        } catch (\Exception $e) {
            Log::error("FileService: Upload failed", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return [
                "success" => false,
                "message" => $e->getMessage(),
            ];
        }
    }

    /**
     * Subir múltiples archivos
     *
     * @param array $files
     * @param string|null $directory
     * @return array
     */
    public function uploadMultiple(
        array $files,
        ?string $directory = null,
    ): array {
        $results = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $results[] = $this->upload($file, $directory);
            }
        }

        return $results;
    }

    /**
     * Descargar un archivo
     *
     * @param string $path
     * @param string|null $filename
     * @return StreamedResponse
     * @throws \Exception
     */
    public function download(
        string $path,
        ?string $filename = null,
    ) {
        if (!Storage::disk("public")->exists($path)) {
            throw new \Exception("Archivo no encontrado");
        }

        $downloadName = $filename ?? basename($path);

        return response()->download(
            Storage::disk("public")->path($path),
            $downloadName,
        );
    }

    /**
     * Eliminar un archivo
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            if (Storage::disk("public")->exists($path)) {
                return Storage::disk("public")->delete($path);
            }
            return true; // Si no existe, consideramos que ya está "eliminado"
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar si un archivo existe
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return Storage::disk("public")->exists($path);
    }

    /**
     * Obtener la URL pública de un archivo
     *
     * @param string $path
     * @return string
     */
    public function getUrl(string $path): string
    {
        return Storage::url($path);
    }

    /**
     * Obtener el tamaño de un archivo en bytes
     *
     * @param string $path
     * @return int
     * @throws \Exception
     */
    public function getSize(string $path): int
    {
        if (!$this->exists($path)) {
            throw new \Exception("Archivo no encontrado");
        }

        return Storage::disk("public")->size($path);
    }

    /**
     * Validar archivo subido
     *
     * @param UploadedFile $file
     * @throws \Exception
     */
    private function validateFile(UploadedFile $file): void
    {
        Log::info("FileService: Validating file");

        // Verificar que el archivo sea válido
        if (!$file->isValid()) {
            $error = "El archivo no es válido. Error: " . $file->getError();
            Log::error("FileService: " . $error);
            throw new \Exception($error);
        }

        // Verificar tamaño máximo
        if ($file->getSize() > $this->maxFileSize) {
            $error =
                "El archivo excede el tamaño máximo permitido de " .
                $this->maxFileSize / 1048576 .
                "MB. Tamaño actual: " .
                $file->getSize() / 1048576 .
                "MB";
            Log::error("FileService: " . $error);
            throw new \Exception($error);
        }

        // Verificar que tenga extensión
        if (!$file->getClientOriginalExtension()) {
            $error = "El archivo debe tener una extensión válida";
            Log::error("FileService: " . $error);
            throw new \Exception($error);
        }

        Log::info("FileService: File validation completed successfully");
    }

    /**
     * Validar si un archivo es una imagen
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isImage(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, $this->allowedImageExtensions);
    }

    /**
     * Redimensionar imagen (requiere intervention/image)
     *
     * @param string $path
     * @param int $width
     * @param int $height
     * @param bool $maintainAspectRatio
     * @return array
     */
    public function resizeImage(
        string $path,
        int $width,
        int $height,
        bool $maintainAspectRatio = true,
    ): array {
        try {
            // Esta funcionalidad requiere la librería intervention/image
            // composer require intervention/image

            return [
                "success" => false,
                "message" =>
                    "Funcionalidad de redimensionamiento requiere la librería intervention/image",
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage(),
            ];
        }
    }

    /**
     * Configurar tamaño máximo de archivo
     *
     * @param int $sizeInBytes
     * @return self
     */
    public function setMaxFileSize(int $sizeInBytes): self
    {
        $this->maxFileSize = $sizeInBytes;
        return $this;
    }

    /**
     * Configurar directorio por defecto
     *
     * @param string $directory
     * @return self
     */
    public function setDefaultDirectory(string $directory): self
    {
        $this->defaultDirectory = $directory;
        return $this;
    }

    /**
     * Obtener información de un archivo
     *
     * @param string $path
     * @return array
     * @throws \Exception
     */
    public function getFileInfo(string $path): array
    {
        if (!$this->exists($path)) {
            throw new \Exception("Archivo no encontrado");
        }

        $fullPath = Storage::disk("public")->path($path);

        return [
            "path" => $path,
            "url" => $this->getUrl($path),
            "size" => $this->getSize($path),
            "mime_type" => mime_content_type(
                Storage::disk("public")->path($path),
            ),
            "last_modified" => Storage::disk("public")->lastModified($path),
            "filename" => basename($path),
            "extension" => pathinfo($path, PATHINFO_EXTENSION),
        ];
    }
}
