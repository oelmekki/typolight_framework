require 'spec'
require 'rubygems'
require 'webrat'
require "#{File.dirname(__FILE__)}/passwd"
require "#{File.dirname(__FILE__)}/logger"
require "#{File.dirname(__FILE__)}/paths"
require 'ruby-debug'


# default object is the session object
def method_missing(name, *args, &block)
  if webrat_session.respond_to?(name)
    webrat_session.send(name, *args, &block)
  else
    super
  end
end

Webrat.configure do |config|
  config.mode = :mechanize
end

module Webrat

  # add a custom logger
  module Logging
    def logger
      Logger.new
    end
  end

  # resolve weird rewriting
  class Link
    def absolute_href
      if href =~ /^\?/
        "#{@session.current_url}#{href}"
      elsif href !~ %r{^https?://} && (href !~ /^\//)
        # "#{@session.current_url}/#{href}"   << wtf? what about current_url == "http://foo/bar.html" and url == "ohmy.html" ?
        "#{APP_URL}/#{href}"
      else
        href
      end
    end
  end

  # prevent webrat to reinitialize the session, by using ||= instead of =
  class MechanizeSession < Session
    def mechanize
      @mechanize ||= WWW::Mechanize.new
    end

  end
end

# in order to be able to do in step definitions :
# webrat_session.page.should have_selector( css3_selector )
module WWW
  class Mechanize
    class Page
      def has_selector?( selector )
        not search( selector ).empty?
      end
    end
  end
end

def webrat_session
  @webrat_session ||= Webrat::MechanizeSession.new
end

Before do
  webrat_session.header( 'Accept-Language', 'fr-fr;q=0.3' );
  webrat_session.basic_auth( ServerUsername, ServerPasswd )
end

