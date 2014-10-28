<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;

class Deprecated extends Change
{
    protected static $function = array(
        'call_user_method'          => 'use call_user_func() instead',
        'call_user_method_array'    => 'use call_user_func_array() instead',
        'define_syslog_variables'   => '',
        'dl'                        => '',
        'ereg'                      => 'use preg_match() instead',
        'ereg_replace'              => 'use preg_replace() instead',
        'eregi'                     => 'use preg_match() with the "i" modifier instead',
        'eregi_replace'             => 'use preg_replace() with the "i" modifier instead',
        'set_magic_quotes_runtime'  => '',
        'magic_quotes_runtime'      => '',
        'session_register'          => 'use the $_SESSION superglobal instead',
        'session_unregister'        => 'use the $_SESSION superglobal instead',
        'session_is_registered'     => 'use the $_SESSION superglobal instead',
        'set_socket_blocking'       => 'use stream_set_blocking() instead',
        'split'                     => 'use preg_split() instead',
        'spliti'                    => 'use preg_split() with the "i" modifier instead',
        'sql_regcase'               => '',
        'mysql_db_query'            => 'use mysql_select_db() and mysql_query() instead',
        'mysql_escape_string'       => 'use mysql_real_escape_string() instead',
        // Passing locale category names as strings is now deprecated. Use the LC_* family of constants instead.
        // The is_dst parameter to mktime(). Use the new timezone handling functions instead.
    );

    public function leaveNode($node)
    {
        if ($node instanceof Expr\FuncCall && !ParserHelper::isDynamicCall($node)) {
            $namestr = $node->name->toString();
            if (!isset(static::$function[$namestr])) {
                return;
            }

            // Function call
            $advice = static::$function[$namestr];
            if ($advice) {
                $errmsg = sprintf('Function %s() is deprecated, %s', $namestr, $advice);
            } else {
                $errmsg = sprintf('Function %s() is deprecated', $namestr);
            }
            $this->visitor->addSpot($errmsg);
        } elseif ($node instanceof Expr\AssignRef && $node->expr instanceof Expr\New_) {
            // Assign new instance
            $this->visitor->addSpot('Assigning the return value of new by reference is deprecated');
        }
    }
}