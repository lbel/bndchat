<?php
/**
 * Objects that have an hash on which they can be uniquely identified
 *
 */
interface Hashable {
  /**
   * @return string hash
   */
  function getHash(); 
}