print('Start #################################################################');

db = db.getSiblingDB('dkc-kz-agg');
db.createUser(
    {
        user: "dkc-user",
        pwd: "dkc-password",
        roles: [
            {
                role: "readWrite",
                db: "dkc-kz-agg"
            }
        ]
    }
);

db.createCollection('users');

print('END #################################################################');
