@inject('idv', 'App\Services\YotiIdvService\YotiIdvService')

<html>
  <head>
    <style>
      @page {
        margin: 130px 50px 100px 50px;
      }

      body {
        color: #3D403D;
      }

      .page-break {
        page-break-after: always;
      }

      .page-number:before {
        content: counter(page);
      }

      #header {
        position: fixed;
        left: 0px;
        right: 0px;
        top: -130px;
        height: 130px;
      }

      #footer {
        position: fixed;
        left: 0px;
        right: 0px;
        bottom: -90px;
        padding-top: 10px;
        border-top: 1px solid black;
      }

      h1, h2, h3 {
        margin: 0;
      }

      table {
        width: 100%;
        border-collapse: collapse;
      }

      th {
        background-color: #E3E3E3;
        text-align: left;
        padding: 10px;
        border: 1px solid black;
      }

      tr {
        border: 1px solid black;
      }

      td {
        padding: 10px 15px;
        vertical-align: top;
      }

      table.no-border tr,
      table.no-border td {
        border: none;
      }

      table.less-padding td, {
        padding: 5px 0px;
      }

      .spacer {
        height: 30px;
      }
    </style>
  </head>
  <body>
    <div id="header">
      <h1 style="float: left; margin-top: 40px;">Identity Verification Session</h1>
      <img style="float: right; margin-top: 40px;" src="data:image/png;base64,{{ base64_encode(file_get_contents(resource_path('img/yoti-logo.png'))) }}" height="40px" />
      <img style="float: right; margin-top: 40px; margin-right: 15px;" src="data:image/png;base64,{{ base64_encode(file_get_contents(resource_path('img/logo.png'))) }}" height="40px" />
    </div>

    <div id="footer">
      <table class="no-border">
        <tr>
          <td>
            Session ID: {{ $session_id }}
          </td>
          <td style="text-align: right;">
            Page <span class="page-number"></span>
          </td>
        </tr>
      </table>
    </div>

    <table>
      <thead>
        <tr>
          <th>
            <h2>Overview</h2>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong>Session ID:</strong><br />
            {{ $session_id }}
          </td>
        </tr>
        <tr>
          <td>
            <strong>Status:</strong><br />
            Completed
          </td>
        </tr>
      </tbody>
    </table>

    <div class="spacer"></div>

    <table>
      <thead>
        <tr>
          <th colspan="2">
            <h2>Recommendations</h2>
          </th>
        </tr>
      </thead>
      <tbody>
        @foreach ($id_documents as $index => $resource)
          @php($recommendation = $checks->first(fn ($c) => in_array($resource->getId(), $c->getResourcesUsed()))->getReport()->getRecommendation()->getValue())
          <tr>
            <td>{{ $idv->documentTypeToText($resource->getDocumentType()) }}</td>
            <td
              @style([
                'color: green' => $recommendation === 'APPROVE',
                'color: red' => $recommendation !== 'APPROVE',
              ])
            >
              {{ $idv->recommendationToText($recommendation) }}
            </td>
          </tr>
        @endforeach

        @foreach ($liveness as $index => $resource)
          @php($recommendation = $checks->first(fn ($c) => in_array($resource->getId(), $c->getResourcesUsed()))->getReport()->getRecommendation()->getValue())
          <tr>
            <td>Liveness capture</td>
            <td
              @style([
                'color: green' => $recommendation === 'APPROVE',
                'color: red' => $recommendation !== 'APPROVE',
              ])
            >
              {{ $idv->recommendationToText($recommendation) }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    @foreach ($id_documents as $index => $resource)
      <div class="page-break"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>ID document {{ $index + 1 }}</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Document ID</strong><br />
              <strong>Document type</strong><br />
              <strong>Issuing authority</strong>
            </td>
            <td>
              {{ $resource->getId() }}<br />
              {{ $idv->documentTypeToText($resource->getDocumentType()) }}<br />
              {{ $resource->getIssuingCountry() }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="spacer"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>Checks performed</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width: 50%">
              <h3>Check details</h3>
            </td>
            <td style="width: 50%;">
              <h3>Breakdown</h3>
            </td>
          </tr>

          @foreach ($checks->filter(fn ($c) => in_array($resource->getId(), $c->getResourcesUsed())) as $check)
            <tr>
              <td>
                <table class="no-border less-padding">
                  <tr>
                    <td><strong>Type</strong></td>
                    <td>{{ $idv->constantToText($check->getType()) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Status</strong></td>
                    <td>{{ $idv->recommendationToText($check->getState()) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Recommendation</strong></td>
                    <td
                      @style([
                        'color: green' => $check->getReport()->getRecommendation()->getValue() === 'APPROVE',
                        'color: red' => $check->getReport()->getRecommendation()->getValue() !== 'APPROVE',
                      ])
                    >
                      {{ $idv->recommendationToText($check->getReport()->getRecommendation()->getValue()) }}
                    </td>
                  </tr>
                </table>
              </td>
              <td>
                <table class="no-border less-padding">
                  @foreach ($check->getReport()->getBreakdown() as $breakdown)
                    <tr>
                      <td>
                        <strong>{{ $idv->breakdownToText($breakdown->getSubCheck()) }}</strong>
                        @foreach($breakdown->getDetails() as $details)
                          <br />
                          <span style="font-size: 70%">
                            {{ $idv->breakdownToText($details->getName()) }}: {{ $details->getValue() }}
                          </span>
                        @endforeach
                      </td>
                      <td
                        @style([
                          'color: green' => $breakdown->getResult() === 'PASS',
                          'color: red' => $breakdown->getResult() !== 'PASS',
                        ])
                      >
                        {{ $idv->recommendationToText($breakdown->getResult()) }}
                      </td>
                    </tr>
                  @endforeach
                </table>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="page-break"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>ID document {{ $index + 1 }}</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Document ID</strong><br />
              <strong>Document type</strong><br />
              <strong>Issuing authority</strong>
            </td>
            <td>
              {{ $resource->getId() }}<br />
              {{ $idv->documentTypeToText($resource->getDocumentType()) }}<br />
              {{ $resource->getIssuingCountry() }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="spacer"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>Text Extraction</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          @php($docFieldsId = $resource->getDocumentFields()?->getMedia()?->getId())
          @if($docFieldsId)
            @foreach (json_decode($idv->getMedia($session_id, $docFieldsId)->getContent()) as $key => $value)
              <tr>
                <td><strong>{{ $idv->extractionToText($key) }}</strong></td>
                <td>
                  @if(is_string($value))
                    {{ $value }}
                  @else
                    @foreach ((array) $value as $k => $v)
                      {{ $idv->extractionToText($k) }}: {{ $v }}<br />
                    @endforeach
                  @endif
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="2" style="text-align: center;">No text extraction data available</td>
            </tr>
          @endif
        </tbody>
      </table>

      <div class="page-break"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>ID document {{ $index + 1 }}</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Document ID</strong><br />
              <strong>Document type</strong><br />
              <strong>Issuing authority</strong>
            </td>
            <td>
              {{ $resource->getId() }}<br />
              {{ $idv->documentTypeToText($resource->getDocumentType()) }}<br />
              {{ $resource->getIssuingCountry() }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="spacer"></div>

      <table>
        <thead>
          <tr>
            <th>
              <h2>Document media</h2>
            </th>
          </tr>
        </thead>
      </table>

      <table class="no-border">
        <tbody>
          @php($media = [])
          @foreach ($resource->getPages() as $page)
            @php($media[] = $page->getMedia()->getId())
            @foreach ($page->getFrames() as $frame)
              @php($media[] = $frame->getMedia()->getId())
            @endforeach
          @endforeach
          <tr>
          @foreach ($media as $index => $mediaId)
              <td style="text-align: center;">
                <img style="max-height: 300px; max-width: 300px;" src="{{ $idv->getMedia($session_id, $mediaId)->getBase64Content() }}" />
              </td>
              @if ($index % 2 === 1)
                </tr>
                <tr>
              @endif
            @endforeach
          </tr>
        </tbody>
      </table>
    @endforeach

    @foreach ($liveness as $index => $resource)
      <div class="page-break"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>Liveness capture</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Liveness capture</strong>
            </td>
            <td>
              {{ collect($resource->getFrames())->filter(fn ($f) => $f->getMedia() !== null)->count() }} images collected
            </td>
          </tr>
        </tbody>
      </table>

      <div class="spacer"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>Checks performed</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width: 50%">
              <h3>Check details</h3>
            </td>
            <td style="width: 50%;">
              <h3>Breakdown</h3>
            </td>
          </tr>

          @foreach ($checks->filter(fn ($c) => is_a($c, Yoti\DocScan\Session\Retrieve\LivenessCheckResponse::class)) as $check)
            <tr>
              <td>
                <table class="no-border less-padding">
                  <tr>
                    <td><strong>Type</strong></td>
                    <td>{{ $idv->constantToText($check->getType()) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Status</strong></td>
                    <td>{{ $idv->recommendationToText($check->getState()) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Recommendation</strong></td>
                    <td
                      @style([
                        'color: green' => $check->getReport()->getRecommendation()->getValue() === 'APPROVE',
                        'color: red' => $check->getReport()->getRecommendation()->getValue() !== 'APPROVE',
                      ])
                    >
                      {{ $idv->recommendationToText($check->getReport()->getRecommendation()->getValue()) }}
                    </td>
                  </tr>
                </table>
              </td>
              <td>
                <table class="no-border less-padding">
                  @foreach ($check->getReport()->getBreakdown() as $breakdown)
                    <tr>
                      <td><strong>{{ $idv->breakdownToText($breakdown->getSubCheck()) }}</strong></td>
                      <td
                        @style([
                          'color: green' => $breakdown->getResult() === 'PASS',
                          'color: red' => $breakdown->getResult() !== 'PASS',
                        ])
                      >
                        {{ $idv->recommendationToText($breakdown->getResult()) }}
                      </td>
                    </tr>
                  @endforeach
                </table>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="page-break"></div>

      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>Liveness capture</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Liveness capture</strong>
            </td>
            <td>
              {{ collect($resource->getFrames())->filter(fn ($f) => $f->getMedia() !== null)->count() }} images collected
            </td>
          </tr>
        </tbody>
      </table>

      <div class="spacer"></div>

      <table>
        <thead>
          <tr>
            <th>
              <h2>Collected images</h2>
            </th>
          </tr>
        </thead>
      </table>

      <table class="no-border">
        <tbody>
          @php($media = [])
          @foreach ($resource->getFrames() as $frame)
            @if ($frame->getMedia() !== null)
              @php($media[] = $frame->getMedia()->getId())
            @endif
          @endforeach
          <tr>
            @foreach ($media as $index => $mediaId)
              <td style="text-align: center;">
                <img style="max-height: 300px; max-width: 300px;" src="{{ $idv->getMedia($session_id, $mediaId)->getBase64Content() }}" />
              </td>
              @if ($index % 2 === 1)
                </tr>
                <tr>
              @endif
            @endforeach
          </tr>
        </tbody>
      </table>
    @endforeach
  </body>
</html>