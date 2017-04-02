<?php
/*
 * A class with the purpose of hashing a given input. Implementation details
 * consist of calls to a hashing library.
 */

class Hasher {

    public function hash(string $password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verify(string $password, string $storedhash) {
        return password_verify($password, $storedhash);
    }

    /*
     * At the moment, this class looks useless, but it exists to separate
     * concerns of hashing from the rest of the system. This becomes very
     * useful if/when we decide to use an external library or change our
     * method later; only these methods in this class need be changed.
     */
}