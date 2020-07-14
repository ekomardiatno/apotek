+function ($) {
  'use strict'
  var timeout = null
  var xhr = new XMLHttpRequest()
  $.fn.autocomplete = function (apiUrl, options) {
    return this.each(function () {
      var $this = $(this)
      var wrapper = $this.parent()
      $this.on('focus',function () {
        options.onStart()
      })
      $this.on('keydown', function (e) {
        if(e.which === 13 && wrapper.find('.option-wrapper').length) {
          e.preventDefault()
        }
      })
      $this.on('keyup', function (e) {
        options.onTyping($(this).val())
        if(e.which !== 37 && e.which !== 38 && e.which !== 39 && e.which !== 40 && e.which !== 13) {
          var $this = $(this)
          wrapper.find('.option-wrapper').remove()
          $this.siblings('.load-data').remove()
          if(timeout !== null) {
            clearTimeout(timeout)
          }
          if(xhr.readyState > 0) {
            xhr.abort()
          }
          timeout = setTimeout(function () {
            if($this.val() !== '') {
              var val = $this.val()
              var formData = new FormData()
              formData.append('queries', val)
              if(options.body) {
                Object.keys(options.body).map(function (key, i) {
                  formData.append(key, Object.values(options.body)[i])
                })
              }
              xhr.open('POST', apiUrl, true)
              xhr.onload = function () {
                var response = JSON.parse(this.responseText)
                if(response.length > 0) {
                  wrapper.append(
                    '<div class="option-wrapper" style="top:' + wrapper.height() + 'px">'+
                    '</div>'
                  )
                  response.map(function (a, i) {
                    wrapper.children('.option-wrapper').append(
                      '<button type="button" data-value="' + a.value + '" class="option' + (i === 0 ? ' hover' : '') + '">' + a.text + '</button>'
                    )
                  })
                }
              }
              xhr.onerror = function () {
                alert('Error!')
              }
              xhr.onloadstart = function () {
                $this.after(
                  '<div class="load-data">'+
                    '<img src="./assets/images/loading.gif"/>'+
                  '</div>'
                )
              }
              xhr.onloadend = function () {
                $this.siblings('.load-data').remove()
              }
              xhr.send(formData)
            }
          }.bind(this), 300)
        } else if (e.which === 40) {
          if(wrapper.find('.option-wrapper').length) {
            var option = wrapper.find('.option-wrapper').find('button.option')
            var optionHoveredIndex = wrapper.find('.option-wrapper').find('button.option.hover').index()
            option.removeClass('hover')
            if(optionHoveredIndex < (option.length - 1)) {
              option.eq(optionHoveredIndex + 1).addClass('hover')
              if(wrapper.find('.option-wrapper').find('button.option.hover').offset().top - wrapper.find('.option-wrapper').offset().top >= wrapper.find('.option-wrapper').innerHeight() - 42) {
                wrapper.find('.option-wrapper').scrollTop((optionHoveredIndex + 1 - 3) * 42)
              }
            } else {
              option.eq(0).addClass('hover')
              wrapper.find('.option-wrapper').scrollTop(0)
            }
          }
        } else if (e.which === 38) {
          if(wrapper.find('.option-wrapper').length) {
            var option = wrapper.find('.option-wrapper').find('button.option')
            var optionHoveredIndex = wrapper.find('.option-wrapper').find('button.option.hover').index()
            option.removeClass('hover')
            if(optionHoveredIndex > 0) {
              option.eq(optionHoveredIndex - 1).addClass('hover')
              if(wrapper.find('.option-wrapper').find('button.option.hover').offset().top - wrapper.find('.option-wrapper').offset().top <= 6) {
                wrapper.find('.option-wrapper').scrollTop((optionHoveredIndex - 2) * 42)
              }
            } else {
              option.eq(option.length - 1).addClass('hover')
              wrapper.find('.option-wrapper').scrollTop((option.length - 5) * 42)
            }
          }
        } else if (e.which === 13) {
          if(wrapper.find('.option-wrapper').length) {
            var optionHovered = wrapper.find('.option-wrapper').find('button.option.hover')
            optionHovered.click()
          }
        }
      })

      wrapper.on('click', 'button.option', function () {
        $this.val($(this).attr('data-value'))
        wrapper.find('.option-wrapper').remove()
        options.onSelect($(this).attr('data-value'))
      })

      wrapper.on('mouseover', 'button.option', function () {
        wrapper.find('button.option').removeClass('hover')
        $(this).addClass('hover')
      })

      $(document).click(function (e) {
        if(!$(e.target).is(wrapper.find('.option-wrapper')) && !$(e.target).is(wrapper.find('.option-wrapper').find('button.option'))) {
          wrapper.find('.option-wrapper').remove()
          if(timeout !== null) {
            clearTimeout(timeout)
            xhr.abort()
          }
        }
      })
    })
  }
}(jQuery)