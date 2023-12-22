<html>
  <head>
    <style>
      {{ file_get_contents(resource_path('css/form-pdfs.css')) }}
    </style>
  </head>
  <body>
    <div id="header">
      <h3 style="float: left; margin-top: 20px;" class="text-primary">www.proconvey.co.uk</h3>
      <img style="float: right; margin-top: 20px; margin-right: 15px;" src="data:image/png;base64,{{ base64_encode(file_get_contents(resource_path('img/logo.png'))) }}" height="50px" />
    </div>

    <div id="footer">
      <table class="no-border">
        <tr>
          <td>
            &copy; ProConvey Limited {{ date('Y') }}
          </td>
          <td style="text-align: right;">
            ProConvey - {{ $form->name }}
            <span style="margin: 0 20px;">|</span>
            Page <span class="page-number"></span>
          </td>
        </tr>
      </table>
    </div>

    <h1>{{ $form->name }}</h1>

    <div class="spacer"></div>

    @foreach ($form->sections as $sectionIndex => $section)
      <table>
        <thead>
          <tr>
            <th>
              <span class="text-primary">Section {{ $sectionIndex + 1 }}:</span> {{ $section->name }}
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach ($section->steps as $stepIndex => $step)
            <tr>
              <td>
                <div>
                  {{ $sectionIndex + 1 }}.{{ $stepIndex + 1 }}. {{ $step->question }}
                </div>

                @if ($step->repeatable_answer_id !== null && array_key_exists($step->answers->first()->id, $providedAnswers))

                  @foreach ($providedAnswers[$step->answers->first()->id] as $index => $_)
                    <br />
                    @foreach ($step->answers as $answer)
                      @if (array_key_exists($answer->id, $providedAnswers))
                        @switch($answer->type)
                          @case('address')
                            <br />
                            <x-answer-types.address :address="$providedAnswers[$answer->id][$index]->value" />
                            @break

                          @case('single_select')
                            <br />
                            <x-answer-types.single-select :options="$answer->details->options" :value="$providedAnswers[$answer->id][$index]->value" />
                            @break

                          @case('dropdown')
                            <br />
                            <x-answer-types.dropdown :value="$providedAnswers[$answer->id][$index]->value" />
                            @break

                          @case('text')
                            <br />
                            <x-answer-types.text :value="$providedAnswers[$answer->id][$index]->value" />
                            @break

                          @case('checkbox')
                            <x-answer-types.checkbox :value="$providedAnswers[$answer->id][$index]->value" :label="$answer->details->label" />
                            @break

                          @case('number')
                            <br />
                            <x-answer-types.number :value="$providedAnswers[$answer->id][$index]->value" />
                            @break

                          @default
                        @endswitch
                      @endif
                    @endforeach

                    @if (!$loop->last)
                      <br />
                    @endif
                  @endforeach

                @else

                  @foreach ($step->answers as $answer)
                    @if (array_key_exists($answer->id, $providedAnswers))
                      @foreach ($providedAnswers[$answer->id] as $providedAnswer)
                        @if ($providedAnswer->value !== null)
                          @switch($answer->type)
                            @case('address')
                              <br />
                              <x-answer-types.address :address="$providedAnswer->value" />
                              @break

                            @case('single_select')
                              <x-answer-types.single-select :options="$answer->details->options" :value="$providedAnswer->value" />
                              @break

                            @case('dropdown')
                              <br />
                              <x-answer-types.dropdown :value="$providedAnswer->value" />
                              @break

                            @case('text')
                              <br />
                              <x-answer-types.text :value="$providedAnswers[$answer->id][$index]->value" />
                              @break

                            @case('checkbox')
                              <x-answer-types.checkbox :value="$providedAnswers[$answer->id][$index]->value" :label="$answer->details->label" />
                              @break

                            @case('number')
                              <br />
                              <x-answer-types.number :value="$providedAnswers[$answer->id][$index]->value" />
                              @break

                            @default
                          @endswitch
                        @endif
                      @endforeach
                    @endif
                  @endforeach

                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      @if(!$loop->last)
        <div class="spacer"></div>
      @endif
    @endforeach
  </body>
</html>
