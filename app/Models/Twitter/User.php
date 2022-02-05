<?php declare(strict_types=1);

namespace App\Models\Twitter;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Twitter User Model
 *
 */
class User implements Arrayable {
    protected string    $id                = "";
    protected string    $username          = "";
    protected string    $niceName          = "";
    protected string    $avatarUrl         = "";
    protected bool      $verifiedProfile   = false;
    protected Carbon    $createdAt;

    public function __construct(string $id, string $username, string $niceName, string $avatarUrl, Carbon $createdAt, bool $verifiedProfile) {
        $this->setId($id)->setUsername($username)->setNiceName($niceName)
             ->setAvatarUrl($avatarUrl)->setCreatedAt($createdAt)->setVerification($verifiedProfile);
    }

    /**
     * Creates an instance of Twitter User from an array matching the
     * Twitter API v2 response structure.
     *
     * @param array $data
     * @return User|null
     */
    public static function createFromArray(array $data) : ?User {
        $required       = ['id', 'username', 'name', 'profile_image_url', 'created_at', 'verified'];

        foreach ($required as $field) {
            if (! array_key_exists($field, $data)) {
                return null;
            }
        }

        return new User(
            $data['id'],
            $data['username'],
            $data['name'],
            $data['profile_image_url'],
            new Carbon($data['created_at']),
            $data['verified'],
        );
    }

    public function getId() : string {
        return $this->id;
    }

    public function setId(string $id) : self {
        $this->id               = $id;
        return $this;
    }

    public function getUsername() : string {
        return $this->username;
    }

    public function setUsername(string $username) : self {
        $this->username         = $username;
        return $this;
    }

    public function getNiceName() : string {
        return $this->niceName;
    }

    public function setNiceName(string $niceName) : self {
        $this->niceName         = $niceName;
        return $this;
    }

    public function getAvatarUrl() : string {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $avatarUrl) : self {
        $this->avatarUrl        = $avatarUrl;
        return $this;
    }

    public function getVerification() : bool {
        return $this->verifiedProfile;
    }

    public function setVerification(bool $verified) : self {
        $this->verifiedProfile      = $verified;
        return $this;
    }

    public function isVerified() : bool {
        return $this->getVerification();
    }

    public function setAsVerified() : self {
        return $this->setVerification(true);
    }

    public function setAsUnverified() : self {
        return $this->setVerification(false);
    }

    public function getCreatedAt() : Carbon {
        return $this->createdAt;
    }

    public function setCreatedAt(Carbon $createdAt) : self {
        $this->createdAt            = $createdAt;
        return $this;
    }

    public function toArray() {
        return [
            'id'            => $this->getId(),
            'username'      => $this->getUsername(),
            'nice_name'     => $this->getNiceName(),
            'verified'      => $this->isVerified(),
            'avatar_url'    => $this->getAvatarUrl(),
            'created_at'    => $this->getCreatedAt()->toISOString(),
        ];
    }
}
