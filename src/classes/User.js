class User {
    constructor(id, email, name) {
        this.id = id;
        this.email = email;
        this.name = name;
    }

    verify() {
        return (this.id ?? this.name ?? this.email ?? false) !== false;
    }
}

export default User;
