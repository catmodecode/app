import jwt from 'jsonwebtoken'

class Token {
    constructor(token) {
        this.token = token;
        this.pubKey = require('../../../jwtRS256.key')
    }

    validate() {
        jwt.verify(this.token, this.pubKey);
    }
}

export default Token;
