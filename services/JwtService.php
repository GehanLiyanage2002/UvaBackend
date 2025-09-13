<?php
// services/JwtService.php
class JwtService {
    private string $secret = '829hT@*U#Ujabc^!&#HDGBh9u38e@&!^!Ybajxyu';
    private string $algo = 'HS256'; // HMAC-SHA256
    private int $ttlSeconds = 60 * 60 * 6; // 6 hours

    private function b64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function sign(string $data): string {
        return $this->b64url(hash_hmac('sha256', $data, $this->secret, true));
    }

    public function generate(array $payload, ?int $ttl = null): string {
        $header = ['typ'=>'JWT','alg'=>$this->algo];
        $now = time();
        $payload = array_merge([
            'iat'=>$now,
            'exp'=>$now + ($ttl ?? $this->ttlSeconds)
        ], $payload);

        $h = $this->b64url(json_encode($header));
        $p = $this->b64url(json_encode($payload));
        $sig = $this->sign("$h.$p");
        return "$h.$p.$sig";
    }

    public function verify(string $jwt): ?array {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;
        [$h, $p, $s] = $parts;

        $check = $this->sign("$h.$p");
        if (!hash_equals($check, $s)) return null;

        $payload = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
        if (!is_array($payload)) return null;
        if (($payload['exp'] ?? 0) < time()) return null;

        return $payload;
    }
}
