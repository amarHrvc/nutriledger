# 07 — Multimodal (Images, Audio, Vision)

## Images — Generation

```php
use Laravel\Ai\Image;

// Basic
$image = Image::of('A donut on a kitchen counter')->generate();

// With options
$image = Image::of('Product mockup on white background')
    ->quality('high')      // 'standard' | 'high'
    ->landscape()          // or ->portrait() or ->square()
    ->timeout(120)
    ->generate();

// Store result
$path = $image->store();              // auto-named
$path = $image->storeAs('logo.jpg'); // named
```

**Providers:** OpenAI (DALL-E), Gemini, xAI  
**Queue it:** `Image::of('...')->queue(provider: Lab::OpenAI)` → see `08-production.md`

### Image Editing (Reference Images)

```php
use Laravel\Ai\Files;

$edited = Image::of('Update this photo to impressionist style')
    ->attachments([Files\Image::fromStorage('photo.jpg')])
    ->landscape()
    ->generate();
```

### Testing

```php
use Laravel\Ai\Image;

Image::fake();
Image::fake([base64_encode($imageBytes)]);
Image::assertGenerated(fn ($prompt) => $prompt->contains('donut'));
Image::assertNothingGenerated();
```

---

## Audio — Text-to-Speech

```php
use Laravel\Ai\Audio;

$audio = Audio::of('Welcome to NutriBase.')->generate();

// With voice options
$audio = Audio::of('Your appointment is confirmed.')
    ->female()                         // or ->male()
    ->instructions('Speak slowly and clearly')
    ->generate();

$path = $audio->store();
$path = $audio->storeAs('welcome.mp3');
```

**Providers:** OpenAI, ElevenLabs

### Testing

```php
Audio::fake();
Audio::assertGenerated(fn ($prompt) => $prompt->contains('Welcome') && $prompt->isFemale());
Audio::assertNothingGenerated();
```

---

## Transcription — Speech-to-Text

```php
use Laravel\Ai\Transcription;

// From file path
$transcript = Transcription::fromPath('/home/laravel/audio.mp3')->generate();

// From storage
$transcript = Transcription::fromStorage('recordings/meeting.mp3')->generate();

// From uploaded file
$transcript = Transcription::fromUpload($request->file('audio'))->generate();

// With speaker diarization
$transcript = Transcription::fromStorage('audio.mp3')
    ->diarize()
    ->generate();

echo (string) $transcript;
```

**Providers:** OpenAI, ElevenLabs, Mistral

### Testing

```php
Transcription::fake();
Transcription::fake(['Transcribed text here.', 'Second response.']);
Transcription::assertGenerated(fn ($prompt) => $prompt->isDiarized());
Transcription::assertNothingGenerated();
```

---

## Vision — Image Input to Agent

Pass images as attachments to any vision-capable agent.

```php
use Laravel\Ai\Files;

$response = (new MyAgent)->prompt(
    'What nutritional information is visible in this image?',
    attachments: [Files\Image::fromStorage('label.jpg')]
);
```

**Vision providers:** OpenAI, Anthropic, Gemini  
→ see `02-agents.md` §Attachments for full attachment API
