<?php

declare(strict_types=1);

final class LabCatalogController
{
    public function __construct(private readonly LabCatalogService $service)
    {
    }

    public function listSubstances(): void
    {
        jsonResponse(200, true, $this->service->knownSubstances(), 'Catálogo de sustancias');
    }

    public function listReagents(): void
    {
        jsonResponse(200, true, $this->service->reagents(), 'Catálogo de reactivos');
    }

    public function listReactions(): void
    {
        jsonResponse(200, true, $this->service->reactions(), 'Recetas de reacciones');
    }
}