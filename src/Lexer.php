<?php

namespace WrongWare\SearchParser;

/**
 * Lexer class.
 */
class Lexer
{
    /**
     * Regex for tokens.
     *
     * @var array
     */
    protected $symbols = [
        'T_OPERATOR' => '/^(or|and)/',
        'T_KEY' => '/^([a-zA-Z]+)(?::)/',
        'T_VALUE' => '/^([a-zA-Z0-9]+)/',
        'T_SEPARATOR' => '/^(:)/',
        'T_WHITESPACE' => '/^(\s+)/',
        'T_OPENPAREN' => '/^(\()/',
        'T_CLOSEPAREN' => '/^(\))/',
    ];

    /**
     * Input string.
     *
     * @var string
     */
    protected $input;

    /**
     * Result of lexical analysis
     * Array of tokens.
     *
     * @var array
     */
    protected $tokens = [];

    /**
     * Constructor.
     *
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
    }

    /**
     * Tokenize input string (lexical analysis).
     *
     * @return TokenStream
     */
    public function tokenize()
    {
        $line = $this->input;
        $offset = 0;

        while ($offset < mb_strlen($line)) {
            $token = $this->match($line, $offset);
            $offset += mb_strlen($token->getValue());
            $this->tokens[] = $token;
        }

        $this->tokens[] = new Token('EOS', null);

        return new TokenStream($this->tokens);
    }

    /**
     * Internal tokenizer based on regexp.
     *
     * @param string $line
     * @param int    $offset
     *
     * @throws Exception if cannot match
     */
    protected function match(string $line, int $offset)
    {
        $string = substr($line, $offset);

        foreach ($this->symbols as $name => $pattern) {
            if (preg_match($pattern, $string, $matches)) {
                return new Token($name, $matches[1]);
            }
        }

        throw new \Exception('Unrecognized character in input stream: '.$string[0].' at '.$offset);
    }
}
