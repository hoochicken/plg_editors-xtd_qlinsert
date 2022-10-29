/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2022 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

(() => {
    window.insertQlinsert = (destination, strToBeInserted) => {
        window.Joomla.editors.instances[destination].replaceSelection(strToBeInserted);
        return true;
    }
})();

function qlinsert(x) {
    window.insertQlinsert(x);
}