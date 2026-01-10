<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Support;

use Vigihdev\WpCliModels\UI\Helper\Helper;
use Vigihdev\WpCliModels\UI\Helper\OutputWrapper;
use Vigihdev\WpCliModels\UI\WpCliStyle;

final class ImportIoSpinner
{
    private int $lineLength = 80;

    private const TYPE_SUCCESS = ' ✔ SUCCESS ';
    private const TYPE_FAILED = ' FAILED ';
    private const TYPE_SKIPPED = ' SKIPPED ';


    /**
     * Create a new ImportIoSpinner instance.
     * 
     * @param WpCliStyle $io The WP CLI style instance for output.
     */
    public function __construct(
        private readonly WpCliStyle $io
    ) {}

    /**
     * Create blocks of text with a specified type and style.
     * 
     * @param string $message The process message to display.
     * @return void
     */
    public function start(string $message): void
    {
        $this->io->spinnerStart("<fg=yellow;options=bold>Process {$message}</>");
    }

    /**
     * Stop the spinner with a success message.
     * 
     * @param string $message The success message to display.
     * @return void
     */
    public function success(string $message): void
    {
        $message = implode(\PHP_EOL, $this->createBlocks(
            message: $message,
            type: self::TYPE_SUCCESS,
            style: 'fg=green;options=bold'
        ));
        $this->io->spinnerStop($message);
    }

    /**
     * Stop the spinner with a skipped message.
     * 
     * @param string $message The skipped message to display.
     * @return void
     */
    public function skipped(string $message): void
    {
        $message = implode(\PHP_EOL, $this->createBlocks(
            message: $message,
            type: self::TYPE_SKIPPED,
            style: 'fg=white'
        ));
        $this->io->spinnerStop($message);
    }

    /**
     * Stop the spinner with a failed message.
     * 
     * @param string $message The failed message to display.
     * @return void
     */
    public function failed(string $message): void
    {
        $message = implode(\PHP_EOL, $this->createBlocks(
            message: $message,
            type: self::TYPE_FAILED,
            style: 'fg=red'
        ));
        $this->io->spinnerStop($message);
    }

    /**
     * Stop the spinner with a failed message.
     * 
     * @param string $message The failed message to display.
     * @return void
     */
    public function stop(string $message): void
    {
        $this->failed($message);
    }

    /**
     * Create blocks of text with a specified type and style.
     * 
     * @param string $message The message to be wrapped.
     * @param string $type The type of the block (e.g., success, failed, skipped).
     * @param string $style The style to apply to the block.
     * @return array The array of formatted lines.
     */
    private function createBlocks(string $message, string $type, string $style): array
    {

        $lines = [];
        $type = \sprintf('%s', $type);
        $indentLength = Helper::width($type);
        $lineIndentation = str_repeat(' ', $indentLength);

        $outputWrapper = new OutputWrapper();
        $lines = explode(\PHP_EOL, $outputWrapper->wrap(
            $message,
            $this->lineLength - $indentLength,
            \PHP_EOL
        ));

        foreach ($lines as $i => &$line) {
            if ($i === 0) {
                $line = \sprintf('%s <%s>%s</>', $this->bgBlock($type), $style, $line);
            } else {
                $line = \sprintf(' <%s>%s</>', $style, $lineIndentation . $line);
            }
        }

        return $lines;
    }

    /**
     * Create a background block with a specified type.
     * 
     * @param string $type The type of the block (e.g., success, failed, skipped).
     * @return string The formatted background block.
     */
    private function bgBlock(string $type): string
    {
        $bg = [
            self::TYPE_SUCCESS => \sprintf("<fg=white;bg=green;options=bold>%s</>", self::TYPE_SUCCESS),
            self::TYPE_FAILED => \sprintf("<fg=white;bg=red;options=bold>%s</>", self::TYPE_FAILED),
            self::TYPE_SKIPPED => \sprintf("<fg=white;bg=blue;options=bold>%s</>", self::TYPE_SKIPPED),
        ];
        return $bg[$type] ?? '';
    }
}
