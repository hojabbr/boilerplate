---
name: unifysofttech-laravel-ffmpeg
description: "Provides video/audio processing in Laravel using FFmpeg via the Unify-SoftTech wrapper around pbmedia/laravel-ffmpeg. Activates when performing media conversions (format change, thumbnails, HLS, filtering), working with FFmpeg in Laravel controllers, using job queues for transcoding, adding watermarks, generating previews, or when the user mentions video processing, FFMPEG commands, streaming, HLS, or media encoding."
license: MIT
metadata:
  author: Unify-SoftTech
---

# Unify-SoftTech Laravel FFMPEG Integration

This package wraps **FFmpeg** functionality for Laravel applications, building on the popular `pbmedia/laravel-ffmpeg` integration to make video/audio processing easier.  [oai_citation:1‡GitHub](https://github.com/Unify-SoftTech/unifysofttech-pbmedia-laravel-ffmpeg)

## When to Apply

Activate this skill when:

- Processing and converting video/audio files
- Transcoding media to different formats
- Generating thumbnails or preview images
- Creating HLS streams (HTTP Live Streaming)
- Applying video filters and resizing
- Adding watermarks or overlay content
- Handling FFmpeg operations inside Laravel queues
- Opening media from disk, web URLs, or uploads

## Installation

### Composer Installation

Require the package via Composer:

```bash
composer require pbmedia/laravel-ffmpeg
```

This installs the Laravel FFmpeg integration that Unify-SoftTech’s package is based on.  ￼

⸻

Publish Configuration

Publish the config file:

php artisan vendor:publish --provider="ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider"

This creates the config file where you can tweak FFmpeg settings and logging.  ￼

⸻

Basic Usage

Open Files

Open video or audio files using the facade:

FFMpeg::fromDisk('local')
    ->open('video.mp4');

You can also open uploaded files directly from a request:

FFMpeg::open($request->file('video'));

Or open media from a remote URL:

FFMpeg::openUrl('https://example.com/video.mp4');


⸻

Convert Media

Convert an audio or video file:

FFMpeg::fromDisk('videos')
    ->open('input.mp4')
    ->export()
    ->toDisk('converted')
    ->inFormat(new \FFMpeg\Format\Video\X264)
    ->save('output.mp4');

This uses Laravel’s filesystem for input/output.  ￼

⸻

Monitor Progress

Track conversion progress:

FFMpeg::open('video.mp4')
    ->export()
    ->onProgress(function ($percentage) {
        echo "{$percentage}% done";
    })
    ->toDisk('converted')
    ->inFormat(new \FFMpeg\Format\Video\X264)
    ->save('video_out.mp4');

The callback can also receive remaining time and rate parameters.  ￼

⸻

Advanced Features

Filters

Apply video filters like resizing or clipping:

use FFMpeg\Filters\Video\VideoFilters;

FFMpeg::fromDisk('videos')
    ->open('movie.mp4')
    ->addFilter(function (VideoFilters $filters) {
        $filters->resize(640, 480);
    })
    ->export()
    ->inFormat(new \FFMpeg\Format\Video\X264)
    ->save('movie_small.mp4');


⸻

Watermarks

Add watermarks:

use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;

FFMpeg::open('video.mp4')
    ->addWatermark(function (WatermarkFactory $watermark) {
        $watermark->open('logo.png')
                  ->horizontalAlignment(WatermarkFactory::RIGHT)
                  ->verticalAlignment(WatermarkFactory::BOTTOM);
    })
    ->export()
    ->inFormat(new \FFMpeg\Format\Video\X264)
    ->save('watermarked.mp4');


⸻

Queue Integration

Because FFmpeg jobs can be computationally heavy, you’ll typically dispatch transcoding tasks to Laravel queues:

ProcessVideo::dispatch($mediaPath);

In the handler, use the FFmpeg facade to convert/process the video. Ensure a queue worker (php artisan queue:work) is running to handle jobs.

⸻

Handling Errors

When FFmpeg fails, an exception like EncodingException is thrown. You can access executed commands and error output for debugging.

try {
    // ... transcoding code
} catch (\ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException $e) {
    $command = $e->getCommand();
    $output  = $e->getErrorOutput();
}


⸻

Tips & Best Practices
	•	Install FFmpeg binaries on your system (ffmpeg, ffprobe) before using the package.
	•	Use Laravel filesystem disks for flexible media storage (local, S3, etc.).
	•	Offload heavy conversions to queues to avoid request timeouts.
	•	For live streaming (HLS), break the video into segments (.m3u8) and serve with compatible players.

⸻

Summary

Feature	Purpose
Open files	Load media from disk, upload, or URL
Media conversion	Transcode videos/audios to formats
Progress monitoring	Track FFmpeg processing
Filters & watermarks	Manipulate video content
Queue jobs	Asynchronous processing for heavy tasks
Exceptions	Debug failed FFmpeg commands