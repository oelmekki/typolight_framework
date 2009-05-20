LET_MEH_LOG_FILES_GROW = false

class Logger
  def initialize
    @path = "#{File.dirname(__FILE__)}/logs/webrat.log"
  end

  def debug( message )
    if LET_MEH_LOG_FILES_GROW
      f = File.new( @path, 'a+' )
      f.puts message
      f.close
    end
  end
end
