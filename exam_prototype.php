<?php

declare(strict_types=1);

namespace Auf\Ipt10\DesignPatterns\Creational\Prototype;

use DateTimeImmutable;
use InvalidArgumentException;

class ExamQuestion
{
    public function __construct(
        public string $prompt,
        public int $points
    ) {}

    public function __clone(): void
    {
        
    }
}

class ExaminationPaper
{
    /**
     * @param ExamQuestion[]
     */
    public function __construct(
        public string $courseCode,
        public string $section,
        public DateTimeImmutable $generatedAt,
        public array $questions = []
    ) {}

    public function __clone(): void
    {
       
        $this->generatedAt = new DateTimeImmutable();

        $deepCopiedQuestions = [];
        foreach ($this->questions as $question) {
            $deepCopiedQuestions[] = clone $question;
        }
        $this->questions = $deepCopiedQuestions;
    }

    public function addQuestion(string $prompt, int $points): void
    {
        $this->questions[] = new ExamQuestion($prompt, $points);
    }
}

class ExamPaperRegistry
{
    /** @var array<string, ExaminationPaper> */
    private array $prototypes = [];

    public function register(string $key, ExaminationPaper $prototype): void
    {
        $this->prototypes[$key] = $prototype;
    }

    public function create(string $key): ExaminationPaper
    {
        if (!isset($this->prototypes[$key])) {
            throw new InvalidArgumentException("Prototype template '{$key}' does not exist in registry.");
        }

        return clone $this->prototypes[$key];
    }
}

$baseMidterm = new ExaminationPaper(
    courseCode: 'IPT10',
    section: 'BSIT-3-A',
    generatedAt: new DateTimeImmutable('2026-10-05 08:00:00')
);
$baseMidterm->addQuestion('Explain the GoF Prototype Pattern intent.', 20);
$baseMidterm->addQuestion('Contrast shallow copy vs deep copy in PHP.', 30);

$registry = new ExamPaperRegistry();
$registry->register('ipt10_midterm_base', $baseMidterm);

$sectionBExam = $registry->create('ipt10_midterm_base');
$sectionBExam->section = 'BSIT-3-B';
$sectionBExam->addQuestion('Discuss PHP 8.1 readonly limitations with __clone().', 50);

echo "=== AUF Examination Paper Prototype Registry ===" . PHP_EOL;
echo "Base Template (Section A) Questions count: " . count($baseMidterm->questions) . PHP_EOL;
echo "Cloned Copy   (Section B) Questions count: " . count($sectionBExam->questions) . PHP_EOL;
echo "Base Generated At:   " . $baseMidterm->generatedAt->format('Y-m-d H:i:s') . PHP_EOL;
echo "Cloned Generated At: " . $sectionBExam->generatedAt->format('Y-m-d H:i:s') . PHP_EOL;