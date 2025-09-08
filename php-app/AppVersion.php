<?php
class AppVersion
{
    private string $manualVersion;
    private ?string $gitTag = null;
    private ?string $gitHash = null;
    private string $buildDate;

    public function __construct(string $manualVersion = '1.0.0')
    {
        $this->manualVersion = $manualVersion;
        $this->buildDate = date('Y-m-d H:i:s');

        // Prüfen, ob Git vorhanden ist
        if (is_dir(__DIR__ . '/.git')) {
            $tag = trim(shell_exec('git describe --tags --abbrev=0 2>/dev/null'));
            $hash = trim(shell_exec('git rev-parse --short HEAD 2>/dev/null'));

            if ($tag !== '') {
                $this->gitTag = $tag;
            }
            if ($hash !== '') {
                $this->gitHash = $hash;
            }
        }
    }

    public function getVersion(): string
    {
        return $this->gitTag ?? $this->manualVersion;
    }

    public function getGitHash(): ?string
    {
        return $this->gitHash;
    }

    public function getBuildDate(): string
    {
        return $this->buildDate;
    }

    public function getFullVersion(): string
    {
        $version = $this->getVersion();
        if ($this->gitHash) {
            $version .= " ({$this->gitHash})";
        }
        $version .= " — Build: {$this->buildDate}";
        return $version;
    }

    public function toArray(): array
    {
        return [
            'version' => $this->getVersion(),
            'gitHash' => $this->gitHash,
            'buildDate' => $this->buildDate,
        ];
    }
}
