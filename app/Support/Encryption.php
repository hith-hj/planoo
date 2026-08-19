<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;
use RuntimeException;

class EncryptionFailedException extends RuntimeException {}
class DecryptionFailedException extends RuntimeException {}

class Encryption
{
	private readonly string $key;
	private string $cipher = 'aes-256-gcm';
	private int $ivLength = 12;
	private int $tagLength = 16;

	public function __construct(private readonly string $hexKey)
	{
		if (mb_strlen($hexKey) !== 64) {
			throw new InvalidArgumentException('Provided key is invalid. It must be a 64-character hex string.');
		}
		$this->key = hex2bin($hexKey);
	}

	public function encrypt(mixed $data): string
	{
		$iv = random_bytes($this->ivLength);
		$tag = '';

		$serializedData = serialize($data);

		$cipher = openssl_encrypt(
			data: $serializedData,
			cipher_algo: $this->cipher,
			passphrase: $this->key,
			tag_length: $this->tagLength,
			iv: $iv,
			tag: $tag,
			aad: '',
			options: OPENSSL_RAW_DATA
		);

		if ($cipher === false) {
			throw new EncryptionFailedException('Encryption process failed unexpectedly.');
		}

		$package = json_encode([
			'iv'     => base64_encode($iv),
			'tag'    => base64_encode($tag),
			'cipher' => base64_encode($cipher),
		], JSON_THROW_ON_ERROR);

		return base64_encode($package);
	}

	public function decrypt(string $data): mixed
	{
		$payload = base64_decode($data, true);
		if ($payload === false) {
			throw new DecryptionFailedException('The payload is not a valid.');
		}

		if (!json_validate($payload)) {
			throw new DecryptionFailedException('The underlying payload structure is corrupted');
		}

		$package = json_decode(json: $payload, associative: true, flags: JSON_THROW_ON_ERROR);

		if (!isset($package['iv'], $package['tag'], $package['cipher'])) {
			throw new DecryptionFailedException('The payload package is missing encryption components.');
		}

		$iv = base64_decode($package['iv'], true);
		$tag = base64_decode($package['tag'], true);
		$cipher = base64_decode($package['cipher'], true);

		if ($iv === false || $tag === false || $cipher === false) {
			throw new DecryptionFailedException('Failed to process encoded attributes inside the payload.');
		}

		$raw = openssl_decrypt(
			data: $cipher,
			cipher_algo: $this->cipher,
			passphrase: $this->key,
			options: OPENSSL_RAW_DATA,
			iv: $iv,
			tag: $tag
		);

		if ($raw === false) {
			// The key might be wrong, or data was altered.
			throw new DecryptionFailedException('Decryption failed.');
		}

		return unserialize($raw, ['allowed_classes' => false]);
	}
}
