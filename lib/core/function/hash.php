<?php

function __hash($string, $saltPrefix = '', $saltSuffix = '', $hashAlgo = 'whirlpool'){
    if(in_array($hashAlgo, hash_algos())){
        return hash($hashAlgo, $saltPrefix . $string . $saltSuffix);
    }
    else{
        throw new InvalidArgumentException(__('This hash algorithm doesn\'t exists.'));
    }
}