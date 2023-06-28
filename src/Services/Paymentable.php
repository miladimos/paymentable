<?php

namespace Laravelir\Paymentable\Services;

use Laravelir\Paymentable\Exceptions\DriverNotFoundException;

class Paymentable
{
    protected float $amount;
    protected string $uuid;
    protected ?int $userId = null;
    protected ?string $token = null;
    protected ?string $email = null;
    protected ?string $userName = null;
    protected ?string $invoiceId = null;
    protected ?string $description = null;
    protected ?string $phoneNumber = null;
    protected ?string $transactionId = null;
    protected ?string $callbackUrl = null;

    public function __construct()
    {
        $this->setDriver();
    }

    public function validateDriver()
    {
        if (true) {
            throw new DriverNotFoundException();
        }
    }

    private function setDriver(): void
    {
        $this->validateDriver();

        $class = config($this->getDriverConfigKey());

        $this->driver = new $class($this->getConfigs());
    }

    private function getDriver(): PurchaseInterface
    {
        if (empty($this->driver)) {
            $this->setDefaultGateway();
        }

        return $this->driver;
    }

    public function setTransactionId(string $id): self
    {
        $this->transactionId = $id;

        return $this;
    }

    /**
     * @param  string  $token
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param  string  $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param  string  $phone
     * @return $this
     */
    public function setPhoneNumber(string $phone): self
    {
        $this->phoneNumber = $phone;

        return $this;
    }

    /**
     * @param  string  $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function setUserName(string $name): self
    {
        $this->userName = $name;

        return $this;
    }

    /**
     * @param  int  $userId
     * @return $this
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param  string  $invoiceId
     * @return $this
     */
    public function setInvoiceId(string $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    /**
     * @param  string  $callbackUrl
     * @return $this
     */
    public function setCallbackUrl(string $callbackUrl): self
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return float|int
     */
    public function getAmountInTomans(): float|int
    {
        return $this->amount / 10;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getInvoiceId(): string
    {
        if (empty($this->invoiceId)) {
            $this->invoiceId = crc32($this->getUuid()) . random_int(0, 99999);
        }

        return $this->invoiceId;
    }

    /**
     * @return array
     */
    public function getCustomerInfo(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'phone' => $this->getPhoneNumber(),
            'email' => $this->getEmail(),
        ];
    }

    public function payment($amount)
    {
        $this->checkDriverImplementsInterface(PurchaseInterface::class);

        $transactionId = $this->getDriver()->setInvoice($invoice)->purchase();

        if ($callback) {
            $callback($transactionId);
        }

        return $this->getDriver()->pay();
    }

    public function verify(Invoice $invoice): Receipt
    {
        $this->checkDriverImplementsInterface(PurchaseInterface::class);

        return $this->getDriver()->setInvoice($invoice)->verify();
    }

    public function callback()
    {
    }

    public function setAmount($amount): self
    {
        if (config('multipayment.convert_to_rials')) {
            $this->amount = $amount * 10;
        } else {
            $this->amount = $amount;
        }

        return $this;
    }
}
